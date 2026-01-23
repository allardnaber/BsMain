<?php

namespace BsMain\Api\Jwk;

class ClientCredentialOptions {

	/**
	 * @param string $clientId
	 * @param string $tokenUrl
	 * @param string $privateKey
	 * @param string $scopes
	 * @param int|null $validity Validity of this assertion in seconds
	 * @param string|null $keyId
	 * @return array{client_assertion_type: string, client_assertion: string, scope: string}
	 */
	public static function get(
		string                        $clientId,
		string                        $tokenUrl,
		#[\SensitiveParameter] string $privateKey,
		string                        $scopes,
		?string                       $keyId = null,
		?int                          $validity = null
	): array {
		$assertion = new Assertion($clientId, $tokenUrl, $privateKey, $keyId, $validity);
		return [
			'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
			'client_assertion' => $assertion->getAssertion(),
			'scope' => $scopes
		];
	}

}
