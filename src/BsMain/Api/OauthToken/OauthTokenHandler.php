<?php

namespace BsMain\Api\OauthToken;

use allardnaber\OAuth2\Brightspace\Provider\Brightspace;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class OauthTokenHandler {

	private AccessTokenInterface $accessToken;
	
	public function __construct(private readonly Brightspace $provider, private readonly array $config) {}

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
	
	protected function getConfig(): array {
		return $this->config;
	}

	/**
	 * @throws IdentityProviderException|GuzzleException
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
