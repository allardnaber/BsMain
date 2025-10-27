<?php

namespace BsMain\Api;

use BsMain\Data\ApiEntity;
use BsMain\Data\GenericObject;
use BsMain\Exception\BrightspaceApiException;
use BsMain\Exception\BrightspaceException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use RuntimeException;

/**
 * @template T extends GenericObject
 */
class ApiRequest {

	private const string API_PATH = 'd2l/api/';


	private string $url;
	private ?string $description = null;
	//private ?string $classname = null;
	private ?string $jsonData = null;
	private array $options = [];

	private string $baseUrl;

	public function __construct(
		private readonly RequestMethod $method,
		private readonly string $classname,
		private readonly BsApiClient $client
	) {
		$this->baseUrl = $this->client->getBrightspaceUrl();
	}

	/**
	 * Generates the full API url. This method accepts the standard printf format
	 * and urlencodes the parameters.
	 * @param string $url Base URL with placeholders
	 * @param string[] $values The values to put in the placeholder
	 * @return self<T>
	 */
	public function url1(string $url, string ...$values): self {
		$safeValues = array_map('urlencode', $values);
		$this->url = vsprintf($this->baseUrl . self::API_PATH . $url, $safeValues);
		return $this;
	}

	/**
	 * Generates the full API url. This method accepts the standard printf format
	 * and urlencodes the parameters.
	 * @param string $url Base URL with placeholders
	 * @param string[] $values The values to put in the placeholder
	 * @return self<T>
	 */
	public function url(Service $service, string $url, string ...$values): self {
		$base = $this->baseUrl . self::API_PATH . $service->name . '/';
		if (!empty($service->value)) {
			$base .= $service->value . '/';
		}

		$safeValues = array_map('urlencode', $values);


		$this->url = vsprintf($base . $url, $safeValues);
		return $this;
	}

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
	 * Append provided parameter to the url, if value is not null.
	 * @param string $url Base URL.
	 * @param array $params Array with key/value pairs corresponding to the parameters to add.
	 * @param array $allowed Array with the allowed parameters for the specific call.
	 * @return self<T>
	 */
	public function param(string $name, ?string $value): self {
		if (!isset($this->url)) {
			throw new \RuntimeException('Set the request url before appending parameters.');
		}
		if ($value !== null) {
			$queryStart = str_contains($this->url, '?') ? '&' : '?';
			$this->url .= $queryStart . $name . '=' . urlencode($value);
		}
		return $this;
	}


	/**
	 * Request full data set for calls that return paged results. This method
	 * handles both paged results types that Brightspace offers: paged result sets
	 * and object list pages.
	 *
	 * @param string $url The url to the Brightspace API.
	 * @return T[] Associative array with the decoded values of the full result set. Paging info not included.
	 * @throws IdentityProviderException
	 */
	private function requestPaged(string $url, string $dataType): array {
		$response = json_decode($this->requestRaw($url, $dataType), true);

		if (isset($response['Items'])) {
			return $this->getPagedResultSet($url, $dataType, $response);
		} elseif (isset($response['Objects'])) {
			return $this->getObjectListPage($url, $dataType, $response);
		} else {
			throw new RuntimeException('Unknown paged type result from API. Items and Objects are both unspecified. ' .
				'See https://docs.valence.desire2learn.com/basic/apicall.html#paged-data');
		}
	}

	/**
	 * Handle the Paged Result Set as described on
	 * https://docs.valence.desire2learn.com/basic/apicall.html#Api.PagedResultSet
	 * @param string $url The original url to the Brightspace API.
	 * @param string $dataType
	 * @param mixed $response The initial response for the first page.
	 * @return array
	 * @throws IdentityProviderException
	 */
	private function getPagedResultSet(string $url, string $dataType, mixed $response): array {
		$bookmarkSep = str_contains($url, '?') ? '&' : '?';
		$result = $response['Items'];
		while ($response['PagingInfo']['HasMoreItems']) {
			$bookmark = $response['PagingInfo']['Bookmark'];
			$pagedUrl = sprintf('%s%sbookmark=%s', $url, $bookmarkSep, $bookmark);
			$response = json_decode($this->requestRaw($pagedUrl, $dataType), true);
			$result = array_merge($result, $response['Items']);
		}
		return $result;
	}

	/**
	 * Handle the Object List Page as described on
	 * https://docs.valence.desire2learn.com/basic/apicall.html#object-list-pages
	 * @param string $url
	 * @param string $dataType
	 * @param mixed $response
	 * @return array
	 * @throws IdentityProviderException
	 */
	private function getObjectListPage(string $url, string $dataType, mixed $response): array {
		$result = $response['Objects'];
		while ($response['Next'] !== null) {
			$response = json_decode($this->requestRaw($this->convertNextUrl($response['Next']), $dataType), true);
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
		return str_starts_with($nextUrl, 'https://')
			? $nextUrl
			: $this->client->getBrightspaceUrl() . $nextUrl;
	}


	/**
	 * @return T|null Decoded associative array from raw response.

	 */
	public function fetch(): GenericObject|ApiEntity|null {
		try {
			$result = json_decode($this->requestRaw(), true);

			if ($this->classname === null) {
				echo 'classname unset;';
				return null;
			}
			$resultObj = $this->classname::newInstance($result);
			if (!$resultObj instanceof GenericObject && !$resultObj instanceof ApiEntity) {
				throw new \InvalidArgumentException('Can only create subclasses of ' . GenericObject::class);
			}
			return $resultObj;
		} catch (IdentityProviderException $e) {
			throw new BrightspaceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @param string $url
	 * @param ?string $resultClass
	 * @param bool $paged
	 * @param string $dataType
	 * @param string $method
	 * @param string|null $jsonData
	 * @param array $options
	 * @return T[]|null Decoded associative array from raw response.
	 */
	protected function requestArray(
		string $url,
		?string $resultClass,
		bool $paged,
		string $dataType = 'object',
		string $method = 'GET',
		?string $jsonData = null,
		array $options = []
	): array|null {
		$result = $paged
			? $this->requestPaged($url, $dataType)
			: json_decode($this->requestRaw($url, $dataType, $method, $jsonData, $options), true);
		if ($resultClass === null) {
			return null;
		}
		$resultObj = $resultClass::array($result);
		if (count($resultObj) > 0 && !$resultObj[0] instanceof GenericObject) {
			throw new \InvalidArgumentException('Can only create subclasses of ' . GenericObject::class);
		}
		return $resultObj;
	}

	/**
	 * Perform the API request
	 * @return string Raw response from API
	 * @throws BrightspaceException|IdentityProviderException
	 */
	protected function requestRaw(): string {

		try {
			$request = $this->client->getProvider()->getAuthenticatedRequest(
				$this->method->name, $this->url, $this->client->getTokenHandler()->getAccessToken(), $this->options
			);

			if ($this->jsonData !== null) {
				$this->options[RequestOptions::BODY] = $this->jsonData;
				$this->options[RequestOptions::HEADERS]['Content-Type'] = 'application/json';
			}
			$response = $this->client->getHttp()->send($request, $this->options);
			return $response->getBody()->getContents();
		} catch (RequestException $ex) {
			$status = $ex->getResponse() !== null ? $ex->getResponse()->getStatusCode() : 0;
			throw new BrightspaceApiException($this->method, $this->classname, $status);
		} catch (GuzzleException $ex) {
			throw new BrightspaceException($ex->getMessage());
		}
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
