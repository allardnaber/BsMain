<?php

namespace BsMain\Api\Jwk;

use BsMain\Uuid;
use Firebase\JWT\JWT;

class Assertion {

	public string $sub;
	public string $iss;
	public string $aud;
	public string $jti;
	public int $exp;

	public function __construct(
		string                                         $clientId,
		string                                         $audienceUrl,
		#[\SensitiveParameter] private readonly string $privateKey,
		private readonly ?string                       $keyId = null,
		?int                                           $validity = null
	) {
		$this->sub = $this->iss = $clientId;
		$this->aud = $audienceUrl;
		$this->jti = Uuid::get();
		$this->exp = time() + ($validity ?? 5) * 60;
	}

	public function getAssertion(): string {
		$fields = get_object_vars($this);
		unset($fields['privateKey'], $fields['keyId']);
		return JWT::encode($fields, $this->privateKey, 'RS256', $this->keyId);
	}

}
