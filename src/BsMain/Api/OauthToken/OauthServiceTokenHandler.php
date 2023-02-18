<?php

namespace BsMain\Api\OauthToken;

use League\OAuth2\Client\Token\AccessToken;
use BsMain\Exception\BsAppRuntimeException;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OauthServiceTokenHandler extends OauthTokenHandler {

	public function retrieveAccessToken() {
		$tokenJson = file_get_contents($this->getConfig()['oauth2']['serviceTokenFile']);
		if ($tokenJson === false) {
			throw new BsAppRuntimeException('Error getting Brightspace access token: ' . error_get_last());
		}
		$tokenArr = json_decode($tokenJson, true);
		$this->setAccessToken(new AccessToken($tokenArr));
	}

	public function refreshAccessToken() {
		$newToken = $this->getProvider()->getAccessToken('refresh_token', [
			'refresh_token' => $this->getCurrentAccessToken()->getRefreshToken()
		]);
		$tokenJson = json_encode($newToken->jsonSerialize());

		if (file_put_contents($this->getConfig()['oauth2']['serviceTokenFile'], $tokenJson) === false) {
			throw new BsAppRuntimeException('Error writing new Brightspace access token: ' . error_get_last());
		} else {
			// write succeeded
			$this->setAccessToken($newToken);
		}
	}

	public static function saveAccessToken(AccessTokenInterface $token, $config): void {
		$tokenJson = json_encode($token->jsonSerialize());

		if (file_put_contents($config['oauth2']['serviceTokenFile'], $tokenJson) === false) {
			throw new BsAppRuntimeException('Error writing new Brightspace access token: ' . error_get_last());
		}
	}

}
