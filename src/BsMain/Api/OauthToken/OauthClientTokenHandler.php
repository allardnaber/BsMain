<?php

namespace BsMain\Api\OauthToken;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OauthClientTokenHandler extends OauthTokenHandler {

	const TOKEN_NAME = 'oauth2_token';
	const STATE_NAME = 'oauth2_state';

	/**
	 * Handle retrieving an oauth2 token. This method has three paths:
	 * 1. Receive ?code=: incoming token, process and save to session, only if
	 *    we do not have a token yet (otherwise a Refresh might cause errors
	 *      -> processInitialTokenResponse
	 * 2. No info supplied, try to retrieve token that was acquired earlier in
	 *    this session and refresh if it has expired. -> refreshAccessTokenIfRequired
	 * 3. No token available yet, start authorization process -> getInitialTokenFromuser
	 */
	public function retrieveAccessToken() {
		if (isset($_GET['code']) && isset($_GET['state']) && !isset($_SESSION[self::TOKEN_NAME])) {
			$this->processInitialTokenResponse();
		} elseif (isset($_SESSION[self::TOKEN_NAME])) {
			$tokenArr = json_decode($_SESSION[self::TOKEN_NAME], true);
			$this->setAccessToken(new AccessToken($tokenArr));
		} else {
			$this->getInitialTokenFromUser();
		}
	}


	/**
	 * @throws IdentityProviderException
	 */
	public function refreshAccessToken() {
		$this->setAccessToken($this->getProvider()->getAccessToken(
				'refresh_token',
				['refresh_token' => $this->getCurrentAccessToken()->getRefreshToken()]
		));
		$this->saveTokenToSession();
	}


	/**
	 * Start oauth2 authorization sequence.
	 */
	private function getInitialTokenFromUser() {
		$url = $this->getProvider()->getAuthorizationUrl();
		$_SESSION[self::STATE_NAME] = $this->getProvider()->getState();
		header('Location: ' . $url);
		exit();
	}

	/**
	 * Prevent CSRF attacks by checking if the state is unaltered.
	 * @throws \Exception If we see an unexpected state.
	 */
	private function verifyState() {
		if (empty($_GET['state']) ||
				(isset($_SESSION[self::STATE_NAME]) &&
				$_GET['state'] !== $_SESSION[self::STATE_NAME] )
		) {
			if (isset($_SESSION[self::STATE_NAME])) {
				unset($_SESSION[self::STATE_NAME]);
			}
			throw new \Exception('Invalid state while retrieving access token.');
		}
	}

	private function processInitialTokenResponse() {
		$this->verifyState();
		$this->setAccessToken($this->getProvider()->getAccessToken(
				'authorization_code', ['code' => $_GET['code']]
		));
		$this->saveTokenToSession();
	}

	private function saveTokenToSession() {
		$_SESSION[self::TOKEN_NAME] = json_encode($this->getCurrentAccessToken()->jsonSerialize());
	}

	public static function getTokenFromSession(): ?AccessTokenInterface {
		if (isset($_SESSION[self::TOKEN_NAME])) {
			$tokenArr = json_decode($_SESSION[self::TOKEN_NAME], true);
			return new AccessToken($tokenArr);
		} else {
			return null;
		}
	}

}
