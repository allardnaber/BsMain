<?php

namespace BsMain\Api\Jwk;

class Base64Url {

	public static function encode(mixed $data): string {
		return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	public static function decode(string $data): string|false {
		return base64_decode(str_pad(strtr($data, '-_', '+/'), 4 - ((strlen($data) % 4) ?: 4), '=', STR_PAD_RIGHT));
	}

}
