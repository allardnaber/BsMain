<?php

namespace BsMain\Controller;

use BsMain\Base64Url;
use BsMain\Controller\Attributes\Route;

class JwkController extends BsBaseController {

	#[Route('/jwks')]
	public function jwkPublic(): void {
		header('Content-Type: application/json; charset=utf-8');
		$pub = $this->getConfig('oauth2', 'jwkPublic');
		$key = openssl_get_publickey($pub);

		$details = openssl_pkey_get_details($key);
		$nval = Base64Url::encode($details['rsa']['n']);
		$eval = Base64Url::encode($details['rsa']['e']);


		$result = [
			'kty' => 'RSA',
			'e' => $eval,
			'use' => 'sig',
			'alg' => 'RS256',
			'n' => $nval,
			'kid' => 1
		];
		echo json_encode(['keys' => [$result]], JSON_PRETTY_PRINT);
	}
}