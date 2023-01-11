<?php

namespace BsMain\Api;

use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Exception\BsAppApiException;
use GuzzleHttp\Psr7\Utils;

/**
 * Base class with utilities to interact with the Brightspace API.
 */
class BsApiClient {
	private $config;
	private $provider;
	private $http;
	private $tokenHandler;
	private array $resourceApis = [];

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

	public function getConfig(): array {
		return $this->config;
	}

	/**
	 * @return \League\OAuth2\Client\Provider\GenericProvider
	 */
	public function getProvider(): \League\OAuth2\Client\Provider\GenericProvider {
		return $this->provider;
	}

	/**
	 * @return \GuzzleHttp\Client
	 */
	public function getHttp(): \GuzzleHttp\Client {
		return $this->http;
	}

	/**
	 * @return mixed
	 */
	public function getTokenHandler() {
		return $this->tokenHandler;
	}

	private function getResourceApi(string $api, string $className): BsResourceBaseApi {
		if (!isset($this->resourceApis[$api])) {
			$this->resourceApis[$api] = new $className($this);
		}
		return $this->resourceApis[$api];
	}

	public function users(): BsUsersApi {
		return $this->getResourceApi('users', BsUsersApi::class);
	}

	public function orgs(): BsOrgsApi {
		return $this->getResourceApi('orgs', BsOrgsApi::class);
	}
	public function courses(): BsCoursesApi {
		return $this->getResourceApi('courses', BsCoursesApi::class);
	}
}
