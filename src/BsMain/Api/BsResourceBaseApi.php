<?php

namespace BsMain\Api;

use BsMain\Data\GenericObject;
use BsMain\Exception\BsAppApiException;
use BsMain\Exception\BsAppRuntimeException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use RuntimeException;

abstract class BsResourceBaseApi {

	private BsApiClient $client;

	public function __construct(BsApiClient $client) {
		$this->client = $client;
	}

	/**
	 * Generates the full API url. This method accepts the standard printf format
	 * and urlencodes the parameters.
	 * @param string $url Base URL with placeholders
	 * @param string[] $values The values to put in the placeholder
	 * @return string The final URL to call.
	 */
	protected function url(string $url, string ...$values): string {
		$safeValues = array_map('urlencode', $values);
		return vsprintf($this->client->getConfig('brightspace', 'api') . $url, $safeValues);
	}

	/**
	 * Append provided allowed parameters to the url.
	 * @param string $url Base URL.
	 * @param array $params Array with key/value pairs corresponding to the parameters to add.
	 * @param array $allowed Array with the allowed parameters for the specific call.
	 * @return string The URL with the allowed parameters appended.
	 */
	protected function appendQueryParams(string $url, array $params, array $allowed): string {
		$queryStart = str_contains($url, '?') ? '&' : '?';
		$accepted = array_intersect_key($params, array_flip($allowed));
		$paramsUrl = array_map(fn(string $k, mixed $v): string =>
		sprintf('%s=%s', $k, urlencode($v)),
			array_keys($accepted), array_values($accepted)
		);
		return $url . (count($accepted) === 0 ? '' : $queryStart . join('&', $paramsUrl));
	}

	/**
	 * Request full data set for calls that return paged results. This method
	 * handles both paged results types that Brightspace offers: paged result sets
	 * and object list pages.
	 *
	 * @param string $url The url to the Brightspace API.
	 * @return array Associative array with the decoded values of the full result set. Paging info not included.
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

	 */
	private function getObjectListPage(string $url, string $dataType, mixed $response): array {
		$result = $response['Objects'];
		// different than brightspace|api, because url already includes "/d2l/api"
		$urlPrefix = $this->client->getConfig('brightspace', 'url');
		while ($response['Next'] !== null) {
			$response = json_decode($this->requestRaw($urlPrefix . $response['Next'], $dataType), true);
			$result = array_merge($result, $response['Objects']);
		}
		return $result;
	}

	/**
	 * @param string $url
	 * @param ?string $resultClass
	 * @param ?string $dataType
	 * @param string $method
	 * @param string|null $jsonData
	 * @param array $options
	 * @return mixed Decoded associative array from raw response.
	 */
	protected function request(
		string $url,
		?string $resultClass,
		?string $dataType = 'object',
		string $method = 'GET',
		?string $jsonData = null,
		array $options = []
	): mixed {
		$result = json_decode($this->requestRaw($url, $dataType, $method, $jsonData, $options), true);
		if ($resultClass === null) {
			return null;
		}
		$resultObj = new $resultClass($result);
		if (!$resultObj instanceof GenericObject) {
			throw new \InvalidArgumentException('Can only create subclasses of ' . GenericObject::class);
		}
		return $resultObj;
	}

	/**
	 * @param string $url
	 * @param ?string $resultClass
	 * @param bool $paged
	 * @param string $dataType
	 * @param string $method
	 * @param string|null $jsonData
	 * @param array $options
	 * @return array|null Decoded associative array from raw response.
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
	 * @param string $url
	 * @param string $dataType
	 * @param string $method
	 * @param string|null $jsonData
	 * @param array $options
	 * @return string Raw response from API
	 * @throws BsAppRuntimeException
	 */
	protected function requestRaw(
		string $url,
		string $dataType = 'object',
		string $method = 'GET',
		?string $jsonData = null,
		array $options = []
	): string {

		try {
			$request = $this->client->getProvider()->getAuthenticatedRequest(
				$method, $url, $this->client->getTokenHandler()->getAccessToken(), $options
			);

			if ($jsonData !== null) {
				$request = $request->withBody($this->stringToStream($jsonData));
			}
			$response = $this->client->getHttp()->send($request, $options);
			return $response->getBody()->getContents();
		} catch (RequestException $ex) {
			$status = $ex->getResponse() !== null ? $ex->getResponse()->getStatusCode() : 0;
			throw new BsAppApiException($method, $dataType, $status);
		} catch (GuzzleException $ex) {
			throw new BsAppRuntimeException($ex->getMessage());
		}
	}

	protected function addFileToMultipartOptions(string $visibleName, string $actualFilename, string $contentType, $options = []): array {
		if (!isset($options['multipart'])) {
			$options['multipart'] = [];
		}
		$fileInfo = pathinfo($actualFilename);
		$options['multipart'][] = [
			'name' => $visibleName,
			'contents' => Utils::tryFopen($actualFilename, 'r'),
			'filename' => $fileInfo['basename'],
			'headers' => [
				'Content-Type' => $contentType
			]
		];
		return $options;
	}

	/**
	 * Converts a string into a stream, so it can be sent in a Guzzle request.
	 * @param string $resource The source string.
	 * @return Stream The resulting stream.
	 */
	private function stringToStream(string $resource): Stream {
		$stream = fopen('php://temp', 'r+');
		if ($resource !== '') {
			fwrite($stream, $resource);
			fseek($stream, 0);
		}

		return new Stream($stream);
	}

}