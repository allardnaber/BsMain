<?php

namespace BsMain\Api\OauthToken;

use BsMain\Configuration\Configuration;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class OauthTokenHandler {
	
	private AbstractProvider $provider;
	private Configuration $config;
	private AccessTokenInterface $accessToken;
	
	public function __construct(AbstractProvider $provider, Configuration $config) {
		$this->provider = $provider;
		$this->config = $config;
	}

	/**
	 * @throws IdentityProviderException
	 */
	public function getAccessToken(): AccessTokenInterface {
		if (!isset($this->accessToken)) {
			$this->retrieveAccessToken();
		}
		if ($this->accessToken->hasExpired()) {
			$this->refreshAccessToken();
		}
		return $this->accessToken;
	}

	protected function getCurrentAccessToken(): AccessTokenInterface{
		return $this->accessToken;
	}	
	
	protected function setAccessToken($accessToken): void {
		$this->accessToken = $accessToken;
	}
	
	protected function getProvider(): AbstractProvider {
		return $this->provider;
	}
	
	protected function getFullConfig(): Configuration {
		return $this->config;
	}

	/**
	 * @throws IdentityProviderException
	 */
	protected function renewTokenWithProvider(): void {
		$this->setAccessToken($this->getProvider()->getAccessToken(
			'refresh_token',
			['refresh_token' => $this->getCurrentAccessToken()->getRefreshToken()]
		));
	}
	
	public abstract function retrieveAccessToken(): void;

	/**
	 * @throws IdentityProviderException
	 */
	public abstract function refreshAccessToken(): void;

}
