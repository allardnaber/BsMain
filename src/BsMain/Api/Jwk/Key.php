<?php

namespace BsMain\Api\Jwk;

use BsMain\Exception\BsAppRuntimeException;

class Key implements \JsonSerializable {

	/** @noinspection PhpUnused */
	public string $kty = 'RSA';
	public string $alg = 'RS256';
	public string $use = 'sig';
	public string $n;
	public string $e;
	public ?string $kid;

	public function __construct(string $publicKey, ?string $keyId = null) {
		$this->kid = $keyId;

		$key = openssl_get_publickey($publicKey);
		$details = openssl_pkey_get_details($key);

		if ($details['type'] !== OPENSSL_KEYTYPE_RSA) {
			$keyType = match ($details['type']) {
				OPENSSL_KEYTYPE_EC => 'Elliptic-curve',
				OPENSSL_KEYTYPE_DH => 'Diffie–Hellman',
				default => 'unknown',
			};
			throw new BsAppRuntimeException(sprintf('Public key should be an RSA key, current key is of type %s', $keyType));
		}

		$this->n = Base64Url::encode($details['rsa']['n']);
		$this->e = Base64Url::encode($details['rsa']['e']);
	}

	/**
	 * @return string[]
	 */
	public function jsonSerialize(): array {
		$vars = get_object_vars($this);
		if ($this->kid === null) {
			unset ($vars['kid']);
		}
		return $vars;
	}
}
