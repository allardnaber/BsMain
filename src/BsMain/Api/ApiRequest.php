<?php

namespace BsMain\Api;

use BsMain\Data\ApiEntity;
use BsMain\Exception\BrightspaceApiException;
use BsMain\Exception\BrightspaceException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * @template T extends ApiEntity
 */
class ApiRequest {

	public const array API_VERSION = [
		'lp' => '1.53',
		'le' => '1.88'
	];

	private readonly string $baseUrl;



	private string $url;
	private ?string $description = null;
	private ?string $jsonData = null;
	private array $options = [];

	public function __construct(
		private readonly RequestMethod $method,
		private readonly BsApiClient $client,
		private readonly ?string $classname = null,
	) {
		if ($this->classname !== null && !is_subclass_of($this->classname, ApiEntity::class)) {
			throw new RuntimeException(sprintf('Class %s must be a subclass of ApiEntity.', $this->classname));
		}
		$this->baseUrl = $this->client->getBrightspaceUrl();
	}

	/**
	 * Generates the full API url. This method accepts the standard printf format
	 * and url-encodes the parameters.
	 * @param string $url Base URL with placeholders
	 * @param string[] $values The values to put in the placeholder
	 * @return self<T>
	 */
	public function url(string $url, string ...$values): self {
		$safeValues = array_map('urlencode', $values);
		$this->url = vsprintf($url, $safeValues);
		return $this;
	}

	/**
	 * Generates a Learning Platform url with current version number included.
 	 * @param string $url
	 * @param string ...$values
	 * @return self<T>
	 */
	public function lpUrl(string $url, string ...$values): self {
		return $this->serviceUrl('lp', $url, ...$values);
	}

	/**
	 * Generates a Learning Environment url with current version number included.
	 * @param string $url
	 * @param string ...$values
	 * @return self<T>
	 */
	public function leUrl(string $url, string ...$values): self {
		return $this->serviceUrl('le', $url, ...$values);
	}

	/**
	 * Helper function to generate API service url with version number included,
	 * e.g. converts 'courses/123' for service 'lp' into https://host.brightspace.com/d2l/api/lp/1.53/courses/123
	 * @param string $service
	 * @param string $url
	 * @param string ...$values
	 * @return self<T>
	 */
	private function serviceUrl(string $service, string $url, string ...$values): self {
		$resultUrl = join('/', [ $this->client->getBrightspaceApiUrl(), $service, self::API_VERSION[$service], $url ]);
		return $this->url($resultUrl, ...$values);
	}

	/**
	 * @param string $description
	 * @return self<T>
	 */
	public function description(string $description): self {
		$this->description = $description;
		return $this;
	}

	/**
	 * @param string $json
	 * @return self<T>
	 */
	public function jsonBody(string $json): self {
		$this->jsonData = $json;
		return $this;
	}

	/**
	 * Append provided query parameter to the url, if value is not null.
	 * @param string $name
	 * @param string|null $value
	 * @return self<T>
	 */
	public function param(string $name, ?string $value): self {
		if (!isset($this->url)) {
			throw new RuntimeException('Set the request url before appending parameters.');
		}
		if ($value !== null) {
			$queryStart = str_contains($this->url, '?') ? '&' : '?';
			$this->url .= $queryStart . $name . '=' . urlencode($value);
		}
		return $this;
	}


	/**
	 * Request full data set for calls that return paged results. This method inspects the initial result set to see
	 * if it is paged result set or a plain array. If it is paged, it will use the appropriate paging mechanism
	 * (paged result sets or object list pages) to retrieve all pages.
	 *
	 * @return array Associative array with the decoded values of the full result set. Paging info not included.
	 * @throws IdentityProviderException
	 */
	private function requestPagedIfRequired(array $jsonDecodedResponse): array {
		if (array_is_list($jsonDecodedResponse)) {
			return $jsonDecodedResponse;
		} elseif (isset($jsonDecodedResponse['Items'])) {
			return $this->getPagedResultSet($jsonDecodedResponse);
		} elseif (isset($jsonDecodedResponse['Objects'])) {
			return $this->getObjectListPage($jsonDecodedResponse);
		} else {
			throw new RuntimeException('Unknown paged type result from API. Items and Objects are both unspecified. ' .
				'See https://docs.valence.desire2learn.com/basic/apicall.html#paged-data');
		}
	}

	/**
	 * Handle the Paged Result Set as described on
	 * https://docs.valence.desire2learn.com/basic/apicall.html#Api.PagedResultSet
	 * @param array $response The initial response for the first page.
	 * @return array
	 * @throws IdentityProviderException
	 */
	private function getPagedResultSet(array $response): array {
		$result = $response['Items'];
		while ($response['PagingInfo']['HasMoreItems']) {
			$response = new self(RequestMethod::GET, $this->client)
				->url($this->url)
				->param('bookmark', $response['PagingInfo']['Bookmark'])
				->requestJsonDecoded();
			$result = array_merge($result, $response['Items']);
		}
		return $result;
	}

	/**
	 * Handle the Object List Page as described on
	 * https://docs.valence.desire2learn.com/basic/apicall.html#object-list-pages
	 * @param array $response The initial response for the first page.
	 * @return array
	 * @throws IdentityProviderException
	 */
	private function getObjectListPage(array $response): array {
		$result = $response['Objects'];
		while ($response['Next'] !== null) {
			$response = new self(RequestMethod::GET, $this->client)
				->url($this->convertNextUrl($response['Next']))
				->requestJsonDecoded();

			$result = array_merge($result, $response['Objects']);
		}
		return $result;
	}

	/**
	 * Convert next url so it always contains full url. Brightspace returns three alternatives:
	 *  - https://domain/d2l/api/nextEndpoint (documented behavior)
	 *  - /d2l/api/nextEndpoint (behavior seen in Classlist)
	 * @param string $nextUrl
	 * @return string
	 */
	private function convertNextUrl(string $nextUrl): string {
		return (str_starts_with($nextUrl, 'https://') ? '' : $this->baseUrl) . $nextUrl;
	}


	/**
	 *
	 * @noinspection PhpDocSignatureInspection Because of generic type
	 * @return T Decoded associative array from raw response.
	 */
	public function fetch(): ApiEntity {
		try {
			if ($this->classname === null) {
				throw new RuntimeException('Cannot fetch entity for a request without a class name attached.');
			}

			$result = $this->requestJsonDecoded();
			/** @noinspection PhpUndefinedMethodInspection */
			return $this->classname::newInstance($result);
		} catch (IdentityProviderException $e) {
			throw new BrightspaceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @return T[] Decoded associative array from raw response.
	 */
	public function fetchArray(): array {
		// get initial data and verify if the result set is paged. If so, retrieve all pages.
		$result = $this->requestPagedIfRequired($this->requestJsonDecoded());
		/*$result = $this->paged
			? $this->requestPaged()
			: $this->requestJsonDecoded();*/

		// loop instead of map so we don't need to have the data in memory twice.
		foreach ($result as $key => $value) {
			/** @noinspection PhpUndefinedMethodInspection */
			$result[$key] = $this->classname::newInstance($value);
		}
		return $result;
	}

	/**
	 * Perform the API request
	 * @return ResponseInterface Raw response from API
	 * @throws BrightspaceException|IdentityProviderException
	 */
	protected function requestRaw(): ResponseInterface {

		try {
			$request = $this->client->getProvider()->getAuthenticatedRequest(
				$this->method->name, $this->url, $this->client->getTokenHandler()->getAccessToken(), $this->options
			);

			if ($this->jsonData !== null) {
				$this->options[RequestOptions::BODY] = $this->jsonData;
				$this->options[RequestOptions::HEADERS]['Content-Type'] = 'application/json';
			}
			return $this->client->getHttp()->send($request, $this->options);
		} catch (RequestException $ex) {
			$status = $ex->getResponse() !== null ? $ex->getResponse()->getStatusCode() : 0;
			throw new BrightspaceApiException($this->method, $this->classname ?? '(no data type)', $status);
		} catch (GuzzleException $ex) {
			throw new BrightspaceException($ex->getMessage());
		}
	}

	/**
	 * Perform the API request and get the response as a string.
	 * @throws IdentityProviderException
	 */
	protected function requestString(): string {
		$response = $this->requestRaw();
		return $response->getBody()->getContents();
	}

	/**
	 * Perform the API request and get the returned JSON decoded in an associative array.
	 * @return array
	 * @throws IdentityProviderException
	 */
	protected function requestJsonDecoded(): array {
		$response = $this->requestRaw();
		return json_decode($response->getBody()->getContents(), true);
	}

	//@todo
	protected function addFileToMultipartOptions(string $visibleName, string $actualFilename, string $contentType, $options = []): array {
		if (!isset($options[RequestOptions::MULTIPART])) {
			$options[RequestOptions::MULTIPART] = [];
		}
		$fileInfo = pathinfo($actualFilename);
		$options[RequestOptions::MULTIPART][] = [
			'name' => $visibleName,
			'contents' => Utils::tryFopen($actualFilename, 'r'),
			'filename' => $fileInfo['basename'],
			'headers' => [
				'Content-Type' => $contentType
			]
		];
		return $options;
	}



}
