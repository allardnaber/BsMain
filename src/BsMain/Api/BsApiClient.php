<?php

namespace BsMain\Api;

use BsMain\Api\OauthToken\BrightspaceProvider;
use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Api\OauthToken\OauthTokenHandler;
use BsMain\Api\OauthToken\ServiceAuthType;
use BsMain\Configuration\Configuration;
use BsMain\Data\WhoAmIUser;
use BsMain\Exception\BsAppRuntimeException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Base class with utilities to interact with the Brightspace API.
 */
class BsApiClient {
	private Configuration $config;
	private BrightspaceProvider $provider;
	private ClientInterface $http;
	private OauthTokenHandler $tokenHandler;
	private array $resourceApis = [];

	public function __construct(Configuration $config, $useServiceAccount = false) {
		$this->config = $config;
		$this->provider = new BrightspaceProvider($this->getOauth2Config($useServiceAccount));
		$this->http = new Client();
		$this->createTokenHandler($useServiceAccount);
	}

	/**
	 * @noinspection SpellCheckingInspection
	 * @throws IdentityProviderException|GuzzleException
	 */
	public function whoami(): WhoAmIUser {
		return WhoAmIUser::instance($this->provider->getResourceOwner($this->tokenHandler->getAccessToken())->toArray());
	}

	private function getOauth2Config(bool $useServiceAccount): array {
		$result = $this->config->get('oauth2');
		if ($useServiceAccount && $this->config->getOptional('oauth2', 'serviceAuthType') === 'serviceAccount') {
			$result['clientId'] = $this->config->get('oauth2', 'serviceClientId');
		}
		return $result;
	}
	
	private function createTokenHandler($useServiceAccount): void {
		$useCCAuth = $this->config->getOptional('oauth2', 'serviceAuthType') === 'serviceAccount';
		$this->tokenHandler = $useServiceAccount
			? OauthServiceTokenHandler::get($this->provider, $this->config,
				$useCCAuth ? ServiceAuthType::ServiceAccount : ServiceAuthType::RegularAccount)
			: new OauthClientTokenHandler($this->provider, $this->config);
	}

	public function registerServiceToken(AccessTokenInterface $serviceToken): void {
		if (!$this->tokenHandler instanceof OauthServiceTokenHandler) {
			throw new BsAppRuntimeException('Can only register tokens for service token handlers.');
		}
		$this->tokenHandler->storeServiceToken($serviceToken);
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

	public function quizzes(): BsQuizApi {
		return $this->getResourceApi('quiz', BsQuizApi::class);
	}

	public function grades(): BsGradesApi {
		return $this->getResourceApi('grades', BsGradesApi::class);
	}

	public function assignments(): BsAssignmentApi {
		return $this->getResourceApi('assignments', BsAssignmentApi::class);
	}

	public function awards(): BsAwardsApi {
		return $this->getResourceApi('awards', BsAwardsApi::class);
	}
}
