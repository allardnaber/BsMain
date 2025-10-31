<?php

namespace BsMain\Api;

use allardnaber\OAuth2\Brightspace\Provider\Brightspace;
use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Api\OauthToken\OauthTokenHandler;
use BsMain\Api\Resource\ApiShell;
use BsMain\Api\Resource\CourseApi;
use BsMain\Api\Resource\EnrollmentApi;
use BsMain\Api\Resource\QuizApi;
use BsMain\Data\WhoAmIUser;
use BsMain\Exception\BrightspaceAuthException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Base class with utilities to interact with the Brightspace API.
 */
class BsApiClient {
	private readonly Brightspace $provider;
	private readonly ClientInterface $http;
	private OauthTokenHandler $tokenHandler;
	private array $resourceApis = [];
	/**
	 * @var ApiShell[]
	 */
	private array $nextGenApis = [];

	/**
	 * URL to brightspace environment that has no trailing slash
	 * @var string
	 */
	private readonly string $brightspaceUrl;
	private readonly string $brightspaceApiUrl;


	public function __construct(private readonly array $config, $useServiceAccount = false) {
		$this->provider = new Brightspace($config);
		$this->http = new Client();
		$this->createTokenHandler($useServiceAccount);

		$this->brightspaceUrl = str_ends_with($config['url'], '/') ? substr($config['url'], 0, -1) : $config['url'];
		$this->brightspaceApiUrl = $this->brightspaceUrl . '/d2l/api';
	}

	/**
	 * @noinspection SpellCheckingInspection
	 * @throws IdentityProviderException
	 * @throws GuzzleException
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
			throw new BrightspaceAuthException('Can only register tokens for service token handlers.');
		}
		$this->tokenHandler->setServiceToken($serviceToken);
	}

	/**
	 * @return Brightspace
	 */
	public function getProvider(): Brightspace {
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

	public function getBrightspaceUrl(): string {
		return $this->brightspaceUrl;
	}

	public function getBrightspaceApiUrl(): string {
		return $this->brightspaceApiUrl;
	}

	/** @noinspection PhpUnused */
	public function users(): BsUsersApi {
		return $this->getResourceApi('users', BsUsersApi::class);
	}

	/** @noinspection SpellCheckingInspection, PhpUnused */
	public function orgs(): BsOrgsApi {
		return $this->getResourceApi('orgs', BsOrgsApi::class);
	}

	/** @noinspection PhpUnused */
	public function courses(): BsCoursesApi {
		return $this->getResourceApi('courses', BsCoursesApi::class);
	}

	/** @noinspection PhpUnused */
	public function enrollments(): BsEnrollmentsApi {
		return $this->getResourceApi('enrollments', BsEnrollmentsApi::class);
	}

	/** @noinspection PhpUnused */
	public function groups(): BsGroupsApi {
		return $this->getResourceApi('groups', BsGroupsApi::class);
	}

	/** @noinspection PhpUnused */
	public function sections(): BsSectionsApi {
		return $this->getResourceApi('sections', BsSectionsApi::class);
	}

	/** @noinspection PhpUnused */
	public function content(): BsContentApi {
		return $this->getResourceApi('content', BsContentApi::class);
	}

	/** @noinspection PhpUnused */
	public function quizzes(): BsQuizApi {
		return $this->getResourceApi('quiz', BsQuizApi::class);
	}

	/** @noinspection PhpUnused */
	public function grades(): BsGradesApi {
		return $this->getResourceApi('grades', BsGradesApi::class);
	}

	private function getNextGenApi(string $api, string $className): mixed {
		if (!isset($this->nextGenApis[$api])) {
			$this->nextGenApis[$api] = new $className($this);
		}
		return $this->nextGenApis[$api];
	}

	public function course(): CourseApi {
		return $this->getNextGenApi('courses', CourseApi::class);
	}

	public function enrollment(): EnrollmentApi {
		return $this->getNextGenApi('enrollments', EnrollmentApi::class);
	}

	public function quiz(): QuizApi {
		return $this->getNextGenApi('quiz', QuizApi::class);
	}
}
