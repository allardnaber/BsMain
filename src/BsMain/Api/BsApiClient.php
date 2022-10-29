<?php

namespace BsMain\Api;

use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Exception\BsAppApiException;

/**
 * Base class with utilities to interact with the Brightspace API.
 */
abstract class BsApiClient {
	private $config;
	private $provider;
	private $http;
	private $tokenHandler;

	public function __construct($config, $useServiceAccount = false) {
		$this->config = $config;
		$this->provider = new \League\OAuth2\Client\Provider\GenericProvider($config['oauth2']);
		$this->http = new \GuzzleHttp\Client();
		$this->createTokenHandler($useServiceAccount);
	}
	
	public function whoami() {
		return $this->provider->getResourceOwner($this->tokenHandler->getAccessToken());
	}
	
	private function createTokenHandler($useServiceAccount) {
		if ($useServiceAccount) {
			$this->tokenHandler = new OauthServiceTokenHandler($this->provider, $this->config);
		}
		else {
			$this->tokenHandler = new OauthClientTokenHandler($this->provider, $this->config);
		}
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
		return vsprintf($this->config['brightspace']['api'] . $url, $safeValues);
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
		$bookmarkSep = strstr($url, '?') === false ? '?' : '&';
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
		$urlPrefix = $this->config['brightspace']['url'];
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
	 * @return type
	 * @throws BsAppApiException If the API reports an error. This exception
	 *            automatically selects the corresponding error description.
	 */
	protected function request($url, $dataType = 'object', $method = 'GET', $jsonData = null) {
		try {
			$request = $this->provider->getAuthenticatedRequest($method, $url, $this->tokenHandler->getAccessToken());
			if ($jsonData !== null) {
				$request = $request->withBody($this->stringToStream($jsonData));
			}
			$response = $this->http->send($request);
			return $response->getBody()->getContents();
		} catch (\GuzzleHttp\Exception\RequestException $ex) {
			$status = $ex->getResponse() !== null ? $ex->getResponse()->getStatusCode() : 0;
			throw new BsAppApiException($method, $dataType, $status);
		}
	}

	/**
	 * Converts a string into a stream, so it can be sent in a Guzzle request.
	 * @param string $resource The source string.
	 * @return \GuzzleHttp\Psr7\Stream The resulting stream.
	 */
	private function stringToStream(string $resource): \GuzzleHttp\Psr7\Stream {
		$stream = fopen('php://temp', 'r+');
		if ($resource !== '') {
			fwrite($stream, $resource);
			fseek($stream, 0);
		}

		return new \GuzzleHttp\Psr7\Stream($stream);
	}

}
