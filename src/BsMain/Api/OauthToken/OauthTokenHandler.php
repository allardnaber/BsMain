<?php

namespace BsMain\Api\OauthToken;

use BsMain\Configuration\Configuration;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

abstract class OauthTokenHandler {
	
	private $provider, $config;
	private $accessToken;
	
	public function __construct($provider, Configuration $config) {
		$this->provider = $provider;
		$this->config = $config;
		$this->retrieveAccessToken();
	}
	
	public function getAccessToken() {
		if ($this->accessToken->hasExpired()) {
			$this->refreshAccessToken();
		}
		return $this->accessToken;
	}

	protected function getCurrentAccessToken() {
		return $this->accessToken;
	}	
	
	protected function setAccessToken($accessToken) {
		$this->accessToken = $accessToken;
	}
	
	protected function getProvider() {
		return $this->provider;
	}
	
	protected function getFullConfig(): Configuration {
		return $this->config;
	}
	
	public abstract function retrieveAccessToken();

	/**
	 * @throws IdentityProviderException
	 */
	public abstract function refreshAccessToken();

}
