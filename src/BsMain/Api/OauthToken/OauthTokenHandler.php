<?php

namespace BsMain\Api\OauthToken;

abstract class OauthTokenHandler {
	
	private $provider, $config;
	private $accessToken;
	
	public function __construct($provider, $config) {
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
	
	protected function getConfig() {
		return $this->config;
	}
	
	public abstract function retrieveAccessToken();
	
	public abstract function refreshAccessToken();

}
