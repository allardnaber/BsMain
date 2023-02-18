<?php

namespace BsMain\Api;

use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Data\WhoAmIUser;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Base class with utilities to interact with the Brightspace API.
 */
class BsApiClient {
	private array $config;
	private $provider;
	private $http;
	private $tokenHandler;
	private array $resourceApis = [];

	public function __construct($config, $useServiceAccount = false) {
		$this->config = $config;
		$this->provider = new GenericProvider($config['oauth2']);
		$this->http = new Client();
		$this->createTokenHandler($useServiceAccount);
	}
	
	public function whoami(): WhoAmIUser {
		return new WhoAmIUser($this->provider->getResourceOwner($this->tokenHandler->getAccessToken())->toArray());
	}
	
	private function createTokenHandler($useServiceAccount): void {
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
	 * @return GenericProvider
	 */
	public function getProvider(): GenericProvider {
		return $this->provider;
	}

	/**
	 * @return Client
	 */
	public function getHttp(): Client {
		return $this->http;
	}

	/**
	 * @return mixed
	 */
	public function getTokenHandler() {
		return $this->tokenHandler;
	}

	private function getResourceApi(string $api, string $className): mixed {
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
	
	public function enrollments(): BsEnrollmentsApi {
		return $this->getResourceApi('enrollments', BsEnrollmentsApi::class);
	}

	public function groups(): BsGroupsApi {
		return $this->getResourceApi('groups', BsGroupsApi::class);
	}

	public function sections(): BsSectionsApi {
		return $this->getResourceApi('sections', BsSectionsApi::class);
	}
}
