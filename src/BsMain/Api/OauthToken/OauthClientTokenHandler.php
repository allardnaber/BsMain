<?php

namespace BsMain\Api\OauthToken;

use BsMain\Exception\BrightspaceAuthException;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;

class OauthClientTokenHandler extends OauthTokenHandler {

	const string TOKEN_NAME = 'oauth2_token';
	const string STATE_NAME = 'oauth2_state';
	const string REDIRECT_URL = 'redirect_url';

	/**
	 * Handle retrieving an oauth2 token. This method has four paths:
	 * 1. Receive ?code=: incoming token, process and save to session, only if
	 *    we do not have a token yet (otherwise a Refresh might cause errors)
	 *      -> processInitialTokenResponse
	 * 2. No info supplied, try to retrieve token that was acquired earlier in
	 *    this session and refresh if it has expired. -> refreshAccessTokenIfRequired
	 * 3. No access token available yet, check if we have a debug token set and use that one
	 * 4. No token available yet, start authorization process -> getInitialTokenFromUser
	 */
	public function retrieveAccessToken(): void {
		if (isset($_GET['code']) && isset($_GET['state']) && !isset($_SESSION[self::TOKEN_NAME])) {
			$this->processInitialTokenResponse();
		}
		elseif (($sessionToken = $this->getTokenFromSession()) !== null) {
			$this->setAccessToken($sessionToken);
		}
		elseif (($debugToken = ($_ENV['DEBUG_BS_TOKEN'] ?? false)) !== false) {
			$tokenArr = json_decode($debugToken, true);
			$this->setAccessToken(new AccessToken($tokenArr));
			$this->saveTokenToSession();
		}
		else {
			$this->getInitialTokenFromUser();
		}
	}

	/**
	 * @throws IdentityProviderException|GuzzleException
	 */
	public function refreshAccessToken(): void {
		$this->renewTokenWithProvider();
		$this->saveTokenToSession();
	}

	/**
	 * Start oauth2 authorization sequence.
	 */
	private function getInitialTokenFromUser(): void {
		$url = $this->getProvider()->getAuthorizationUrl();
		$_SESSION[self::STATE_NAME] = $this->getProvider()->getState();
		$_SESSION[self::REDIRECT_URL] = $_SERVER['REQUEST_URI'] ?? '';

		$this->redirect($url);
	}

	/**
	 * Prevent CSRF attacks by checking if the state is unaltered.
	 * @throws BrightspaceAuthException If we see an unexpected state.
	 */
	private function verifyState(): void{
		if (empty($_GET['state']) ||
				(isset($_SESSION[self::STATE_NAME]) &&
				$_GET['state'] !== $_SESSION[self::STATE_NAME] )
		) {
			if (isset($_SESSION[self::STATE_NAME])) {
				unset($_SESSION[self::STATE_NAME]);
			}
			throw new BrightspaceAuthException('Invalid state while retrieving access token.');
		}
	}

	private function processInitialTokenResponse(): void {
		$this->verifyState();
		$this->setAccessToken($this->getProvider()->getAccessToken(
				'authorization_code', ['code' => $_GET['code']]
		));
		$this->saveTokenToSession();
		if (!empty($_SESSION[self::REDIRECT_URL] ?? '')) {
			$this->redirect($_SESSION[self::REDIRECT_URL]);
		}
	}

	private function saveTokenToSession(): void {
		$_SESSION[self::TOKEN_NAME] = json_encode($this->getCurrentAccessToken()->jsonSerialize());
	}

	public function getTokenFromSession(): ?AccessTokenInterface {
		if (isset($_SESSION[self::TOKEN_NAME])) {
			$tokenArr = json_decode($_SESSION[self::TOKEN_NAME], true);
			return new AccessToken($tokenArr);
		} else {
			return null;
		}
	}

	private function redirect(string $url): never {
		header('Location: ' . $url);
		http_response_code(302);
		exit;
	}

}
