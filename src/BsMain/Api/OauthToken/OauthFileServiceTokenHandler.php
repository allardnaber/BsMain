<?php

namespace BsMain\Api\OauthToken;

use BsMain\Configuration\Configuration;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use BsMain\Exception\BsAppRuntimeException;

/**
 * Service token handler. The token should be stored in a file on a persistent volume.
 * Write access to the file is guarded with file locks to prevent to separate processes to attempt to renew the
 * token at the same time.
 */
class OauthFileServiceTokenHandler extends OauthServiceTokenHandler {

	private const READ_MODE = 1;
	private const WRITE_MODE = 2;

	private string $tokenFile;

	public function __construct($provider, Configuration $config) {
		parent::__construct($provider, $config);
		$this->tokenFile = $config->get('oauth2', 'serviceTokenFile');
	}

	public function retrieveAccessToken(): void {
		$filePointer = $this->openTokenFile(self::READ_MODE);

		$token = $this->getTokenFromFileReference($filePointer);
		$this->setAccessToken($token);

		$this->closeTokenFile($filePointer);
	}

	/**
	 * @throws IdentityProviderException
	 */
	public function refreshAccessToken(): void {
		$filePointer = $this->openTokenFile(self::WRITE_MODE);
		$token = $this->getTokenFromFileReference($filePointer);

		// Check again to see if the token has expired, it might have been refreshed already.
		// In that case $token contains the newest token, we can proceed with that.
		if ($token->hasExpired()) {
			$this->renewTokenWithProvider();

			$this->writeTokenToFileReference($filePointer, $token);
		}

		$this->closeTokenFile($filePointer);
	}

	/**
	 * Opens the file for reading or writing. For reading a shared lock is required, to make sure we are not reading a
	 * token that is being renewed. For writing an exclusive lock is required to prevent multiple processes to renew
	 * the token simultaneously.
	 *
	 * @param int $mode Read or Write mode, {@see self::READ_MODE} and {@see self::WRITE_MODE}.
	 * @return resource The file pointer
	 */
	private function openTokenFile(int $mode): mixed {
		if ($mode === self::READ_MODE && !is_readable($this->tokenFile)) {
			throw new BsAppRuntimeException('Brightspace service account has not yet been configured or cannot be read.');
		}

		if ($mode === self::WRITE_MODE && (
				(file_exists($this->tokenFile) && !is_writable($this->tokenFile)) ||
				(!file_exists($this->tokenFile) && !is_writable(pathinfo($this->tokenFile, PATHINFO_DIRNAME)))
			)
		) {
			throw new BsAppRuntimeException('Unable to write to token file: Brightspace service account cannot be stored.');
		}

		$filePointer = fopen($this->tokenFile, $mode === self::READ_MODE ? 'r' : 'c+');
		if ($filePointer === false) {
			throw new BsAppRuntimeException('Unable to open file with Brightspace token: ' . error_get_last()['message']);
		}

		if (!flock($filePointer, $mode ===  self::READ_MODE ? LOCK_SH : LOCK_EX)) {
			throw new BsAppRuntimeException('Unable to access Brightspace token file: ' . error_get_last()['message']);
		}

		return $filePointer;
	}

	private function closeTokenFile(mixed $filePointer): void {
		flock($filePointer, LOCK_UN);
		fclose($filePointer);
	}

	private function getTokenFromFileReference(mixed $filePointer): AccessTokenInterface {
		$tokenJson = fgets($filePointer);
		if ($tokenJson === false) {
			throw new BsAppRuntimeException('Error getting Brightspace access token: ' . error_get_last()['message']);
		}

		return new AccessToken(json_decode($tokenJson, true));
	}

	private function writeTokenToFileReference(mixed $filePointer, AccessTokenInterface $token): void {
		// Empty file and write new token
		ftruncate($filePointer, 0);
		rewind($filePointer);
		if (!fwrite($filePointer, json_encode($token->jsonSerialize()))) {
			throw new BsAppRuntimeException('Error writing new Brightspace access token: ' . error_get_last()['message']);
		}
	}

	public function setServiceToken(AccessTokenInterface $serviceToken): void {
		$filePointer = $this->openTokenFile(self::WRITE_MODE, $this->tokenFile);
		$this->writeTokenToFileReference($filePointer, $serviceToken);
		$this->closeTokenFile($filePointer);
	}
}
