<?php

namespace BsMain\Api;

use BsMain\Exception\BsAppApiException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;

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
	protected function url($url, ...$values) {
		$safeValues = array_map('urlencode', $values);
		return vsprintf($this->client->getConfig()['brightspace']['api'] . $url, $safeValues);
	}

	/**
	 * Request full data set for calls that return paged results. This method
	 * handles both paged results types that Brightspace offers: paged result sets
	 * and object list pages.
	 *
	 * @param string $url The url to the Brightspace API.
	 * @return array The full result set with raw data.
	 */
	protected function requestPaged($url, $dataType): array {
		$response = json_decode($this->request($url, $dataType), true);

		if (isset($response['Items'])) {
			return $this->getPagedResultSet($url, $dataType, $response);
		} elseif (isset($response['Objects'])) {
			return $this->getObjectListPage($url, $dataType, $response);
		} else {
			throw new \RuntimeException('Unknown paged type result from API. Items and Objects are both unspecified. ' .
				'See https://docs.valence.desire2learn.com/basic/apicall.html#paged-data');
		}
	}

	/**
	 * Handle the Paged Result Set as described on
	 * https://docs.valence.desire2learn.com/basic/apicall.html#Api.PagedResultSet
	 * @param string $url The original url to the Brightspace API.
	 * @param type $response The initial response for the first page.
	 * @return type
	 */
	private function getPagedResultSet($url, $dataType, $response) {
		$bookmarkSep = str_contains($url, '?') ? '?' : '&';
		$result = $response['Items'];
		while ($response['PagingInfo']['HasMoreItems']) {
			$bookmark = $response['PagingInfo']['Bookmark'];
			$pagedUrl = sprintf('%s%sbookmark=%s', $url, $bookmarkSep, $bookmark);
			$response = json_decode($this->request($pagedUrl, $dataType), true);
			$result = array_merge($result, $response['Items']);
		}
		return $result;
	}

	/**
	 * Handle the Object List Page as described on
	 * https://docs.valence.desire2learn.com/basic/apicall.html#object-list-pages
	 * @param type $url
	 * @param type $response
	 * @return type
	 */
	private function getObjectListPage($url, $dataType, $response) {
		$result = $response['Objects'];
		// different than brightspace|api, because url already includes "/d2l/api"
		$urlPrefix = $this->client->getConfig()['brightspace']['url'];
		while ($response['Next'] !== null) {
			$response = json_decode($this->request($urlPrefix . $response['Next'], $dataType), true);
			$result = array_merge($result, $response['Objects']);
		}
		return $result;
	}

	/**
	 * Perform the API request
	 * @param string $url
	 * @param string $dataType
	 * @param string $method
	 * @param string $jsonData
	 * @return string
	 * @throws BsAppApiException If the API reports an error. This exception
	 *            automatically selects the corresponding error description.
	 */
	protected function request($url, $dataType = 'object', $method = 'GET', $jsonData = null, $options = []): string {
		try {
			$request = $this->client->getProvider()->getAuthenticatedRequest(
				$method, $url, $this->client->getTokenHandler()->getAccessToken(), $options
			);

			if ($jsonData !== null) {
				$request = $request->withBody($this->stringToStream($jsonData));
			}
			$response = $this->client->getHttp()->send($request, $options);
			return $response->getBody()->getContents();
		} catch (\GuzzleHttp\Exception\RequestException $ex) {
			$status = $ex->getResponse() !== null ? $ex->getResponse()->getStatusCode() : 0;
			throw new BsAppApiException($method, $dataType, $status);
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