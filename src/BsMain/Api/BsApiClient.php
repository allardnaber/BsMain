<?php /** @noinspection PhpUnused */

namespace BsMain\Api;

use allardnaber\OAuth2\Brightspace\Provider\Brightspace;
use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Api\OauthToken\OauthTokenHandler;
use BsMain\Api\Resource\ApiShell;
use BsMain\Api\Resource\CourseApi;
use BsMain\Api\Resource\EnrollmentApi;
use BsMain\Api\Resource\QuizApi;
use BsMain\Data\ApiEntity;
use BsMain\Data\WhoAmIUser;
use BsMain\Exception\BrightspaceApiException;
use BsMain\Exception\BrightspaceAuthException;
use BsMain\Exception\BrightspaceException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * Base class with utilities to interact with the Brightspace API.
 * @template T extends ApiEntity
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

	/**
	 * Request full data set for calls that return paged results. This method inspects the initial result set to see
	 * if it is paged result set or a plain array. If it is paged, it will use the appropriate paging mechanism
	 * (paged result sets or object list pages) to retrieve all pages.
	 *
	 * @return array Associative array with the decoded values of the full result set. Paging info not included.
	 * @throws IdentityProviderException
	 */
	private function requestPagedIfRequired(ApiRequest $initialRequest, array $jsonDecodedResponse): array {
		if (array_is_list($jsonDecodedResponse)) {
			return $jsonDecodedResponse;
		} elseif (isset($jsonDecodedResponse['Items'])) {
			return $this->getPagedResultSet($initialRequest, $jsonDecodedResponse);
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
	 * @param ApiRequest<T> $initialRequest
	 * @param array $response The initial response for the first page.
	 * @return array
	 * @throws IdentityProviderException
	 */
	private function getPagedResultSet(ApiRequest $initialRequest, array $response): array {
		$result = $response['Items'];
		while ($response['PagingInfo']['HasMoreItems']) {
			$subRequest = new ApiRequest(RequestMethod::GET)
				->url($initialRequest->getUrl())
				->param('bookmark', $response['PagingInfo']['Bookmark']);
			$response = $this->requestJsonDecoded($subRequest);
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
			$subRequest = new ApiRequest(RequestMethod::GET)->url($response['Next']);
			$response = $this->requestJsonDecoded($subRequest);

			$result = array_merge($result, $response['Objects']);
		}
		return $result;
	}

	/**
	 * Execute an API request that does not return any data.
	 * @param ApiRequest $apiRequest
	 * @return void
	 */
	public function execute(ApiRequest $apiRequest): void {
		try {
			$this->requestRaw($apiRequest);
		} catch (IdentityProviderException $e) {
			throw new BrightspaceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Execute an API request that returns a single entity.
	 * @noinspection PhpDocSignatureInspection Because of generic type
	 * @param class-string<T> $classname The classname of the resulting entity.
	 * @param ApiRequest $apiRequest
	 * @return T
	 */
	public function fetch(string $classname, ApiRequest $apiRequest): ApiEntity {
		if (!is_subclass_of($classname, ApiEntity::class)) {
			throw new RuntimeException(sprintf(
				'Class %s does not extend ApiEntity and cannot be used to fetch data from the Brightspace API',
				$classname));
		}

		try {
			return $classname::newInstance($this->requestJsonDecoded($apiRequest));
		} catch (IdentityProviderException $e) {
			throw new BrightspaceException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * Execute an API request that returns a list of entities, potentially in a paged way.
	 * @param class-string<T> $classname
	 * @param ApiRequest<T> $apiRequest
	 * @return T[] Decoded associative array from raw response.
	 */
	public function fetchArray(string $classname, ApiRequest $apiRequest): array {
		if (!is_subclass_of($classname, ApiEntity::class)) {
			throw new RuntimeException(sprintf(
				'Class %s does not extend ApiEntity and cannot be used to fetch data from the Brightspace API',
				$classname));
		}

		try {
			// get initial data and verify if the result set is paged. If so, retrieve all pages.
			$response = $this->requestJsonDecoded($apiRequest);
			$result = $this->requestPagedIfRequired($apiRequest, $response);
		} catch (IdentityProviderException $e) {
			throw new BrightspaceException($e->getMessage(), $e->getCode(), $e);
		}

		// loop instead of map so we don't need to have the data in memory twice.
		foreach ($result as $key => $value) {
			$result[$key] = $classname::newInstance($value);
		}
		return $result;
	}

	/**
	 * Perform the API request
	 * @param ApiRequest<T> $apiRequest
	 * @return ResponseInterface Raw response from API
	 * @throws BrightspaceException|IdentityProviderException
	 */
	protected function requestRaw(ApiRequest $apiRequest): ResponseInterface {
		try {
			$url = $apiRequest->getUrl();
			if (!str_starts_with($url, 'https:')) {
				$url = $this->brightspaceUrl . $url;
			}
			$options = $apiRequest->getOptions();

			if ($apiRequest->getJsonData() !== null) {
				$options[RequestOptions::BODY] = $apiRequest->getJsonData();
				$options[RequestOptions::HEADERS]['Content-Type'] = 'application/json';
			}
			$request = $this->getProvider()->getAuthenticatedRequest(
				$apiRequest->getMethod()->name, $url, $this->getTokenHandler()->getAccessToken(),
				$options
			);

			return $this->http->send($request, $options);
		} catch (RequestException $ex) {
			$status = $ex->getResponse() !== null ? $ex->getResponse()->getStatusCode() : 0;
			throw new BrightspaceApiException($apiRequest->getMethod(), $apiRequest->getDescription(), $status);
		} catch (GuzzleException $ex) {
			throw new BrightspaceException($ex->getMessage());
		}
	}

	/**
	 * Perform the API request and get the response as a string.
	 * @param ApiRequest<T> $apiRequest
	 * @return string
	 * @throws IdentityProviderException
	 */
	protected function requestString(ApiRequest $apiRequest): string {
		$response = $this->requestRaw($apiRequest);
		return $response->getBody()->getContents();
	}

	/**
	 * Perform the API request and get the returned JSON decoded in an associative array.
	 * @param ApiRequest<T> $apiRequest
	 * @return array
	 * @throws IdentityProviderException
	 */
	protected function requestJsonDecoded(ApiRequest $apiRequest): array {
		$response = $this->requestRaw($apiRequest);
		return json_decode($response->getBody()->getContents(), true);
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
