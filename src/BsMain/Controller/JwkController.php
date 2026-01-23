<?php

namespace BsMain\Controller;

use BsMain\Api\Jwk\Key;
use BsMain\Controller\Attributes\Route;

class JwkController extends BsBaseController {

	#[Route('/jwks')]
	public function jwkPublic(): void {
		$key = new Key(
			$this->getConfig('oauth2', 'jwkPublic'),
			$this->getConfigOptional('oauth2', 'key') ?? 'bsm'
		);

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode([ 'keys' => [ $key ] ], JSON_PRETTY_PRINT);
	}
}