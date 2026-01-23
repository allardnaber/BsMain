<?php

namespace BsMain\Api\OauthToken;

use BsMain\Api\Jwk\ClientCredentialOptions;
use BsMain\Configuration\Configuration;
use BsMain\Exception\BsAppRuntimeException;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Grant\ClientCredentials;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class OauthServiceTokenHandler extends OauthTokenHandler {


	public function __construct(
		BrightspaceProvider     $provider,
		Configuration           $config,
		private ServiceAuthType $authType
	) {
		parent::__construct($provider, $config);
	}

	protected abstract function storeServiceToken(AccessTokenInterface $serviceToken): void;
	protected abstract function getStoredServiceToken(): ?AccessTokenInterface;

	public static function get(BrightspaceProvider $provider, Configuration $config, ServiceAuthType $authType = ServiceAuthType::RegularAccount): self {
		return match ($config->getOptional('oauth2', 'serviceTokenHandler')) {
			'db' => new OauthDatabaseServiceTokenHandler($provider, $config, $authType),
			'file' => new OauthFileServiceTokenHandler($provider, $config, $authType),
			default => throw new BsAppRuntimeException(
				'Service token was requested, but oauth2/serviceTokenHandler was not set to a supported value (db | file).'
			),
		};
	}

	/**
	 * @throws GuzzleException
	 * @throws IdentityProviderException
	 */
	public function retrieveAccessToken(): void {
		$token = $this->getStoredServiceToken();
		if ($token === null) {
			if ($this->authType === ServiceAuthType::RegularAccount) {
				throw new BsAppRuntimeException('Brightspace service account has not yet been configured or cannot be read.');
			} else {
				$token = $this->retrieveClientCredentialAccessToken();
				$this->storeServiceToken($token);
			}
		}
		$this->setAccessToken($token);
	}

	/**
	 * @throws GuzzleException
	 * @throws IdentityProviderException
	 */
	protected function renewTokenWithProvider(): void {
		if ($this->getCurrentAccessToken()?->getRefreshToken() !== null) {
			parent::renewTokenWithProvider();
		} elseif ($this->authType === ServiceAuthType::ServiceAccount) {
			$this->setAccessToken($this->retrieveClientCredentialAccessToken());
		} else {
			throw new BsAppRuntimeException('Brightspace service account token could not be renewed.');
		}
	}

	/**
	 * Retrieves a service token using the client_credentials grant type.
	 * @throws GuzzleException
	 * @throws IdentityProviderException
	 */
	private function retrieveClientCredentialAccessToken(): AccessTokenInterface {
		$grant = new ClientCredentials();
		$options = ClientCredentialOptions::get(
			$this->getProvider()->getClientId(),
			$this->getProvider()->getBaseAccessTokenUrl([]),
			$this->getFullConfig()->get('oauth2', 'jwkPrivate'),
			$this->getFullConfig()->get('oauth2', 'scopes'),
			$this->getFullConfig()->getOptional('oauth2', 'keyId') ?? 'bsm'
		);
		$token = $this->getProvider()->getAccessToken($grant, $options);
		$this->setAccessToken($token);
		return $token;
	}

}
