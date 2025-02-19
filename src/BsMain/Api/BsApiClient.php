<?php

namespace BsMain\Api;

use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthDatabaseServiceTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Api\OauthToken\OauthTokenHandler;
use BsMain\Configuration\Configuration;
use BsMain\Data\WhoAmIUser;
use BsMain\Exception\BsAppRuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Base class with utilities to interact with the Brightspace API.
 */
class BsApiClient {
	private Configuration $config;
	private AbstractProvider $provider;
	private ClientInterface $http;
	private OauthTokenHandler $tokenHandler;
	private array $resourceApis = [];

	public function __construct(Configuration $config, $useServiceAccount = false) {
		$this->config = $config;
		$this->provider = new GenericProvider($config->get('oauth2'));
		$this->http = new Client();
		$this->createTokenHandler($useServiceAccount);
	}

	/**
	 * @noinspection SpellCheckingInspection
	 * @throws IdentityProviderException
	 */
	public function whoami(): WhoAmIUser {
		return WhoAmIUser::instance($this->provider->getResourceOwner($this->tokenHandler->getAccessToken())->toArray());
	}
	
	private function createTokenHandler($useServiceAccount): void {
		$this->tokenHandler = $useServiceAccount
			? OauthServiceTokenHandler::get($this->provider, $this->config)
			: new OauthClientTokenHandler($this->provider, $this->config);
	}

	public function registerServiceToken(AccessTokenInterface $serviceToken): void {
		if (!$this->tokenHandler instanceof OauthServiceTokenHandler) {
			throw new BsAppRuntimeException('Can only register tokens for service token handlers.');
		}
		$this->tokenHandler->setServiceToken($serviceToken);
	}

	public function getFullConfig(): Configuration {
		return $this->config;
	}

	public function getConfig(string ...$path): string|array {
		return $this->config->get(...$path);
	}

	public function getConfigOptional(string ... $path): string|array|null {
		return $this->config->getOptional(...$path);
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

	public function getTokenHandler(): OauthTokenHandler {
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

	/** @noinspection SpellCheckingInspection */
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

	public function content(): BsContentApi {
		return $this->getResourceApi('content', BsContentApi::class);
	}
}
