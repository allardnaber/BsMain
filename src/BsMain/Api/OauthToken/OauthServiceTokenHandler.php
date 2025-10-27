<?php

namespace BsMain\Api\OauthToken;

use allardnaber\OAuth2\Brightspace\Provider\Brightspace;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class OauthServiceTokenHandler extends OauthTokenHandler {

	public abstract function setServiceToken(AccessTokenInterface $serviceToken): void;

	public static function get(Brightspace $provider, array $config): self {
		return match ($config['serviceTokenHandler'] ?? null) {
			'db' => new OauthDatabaseServiceTokenHandler($provider, $config),
			'file' => new OauthFileServiceTokenHandler($provider, $config),
			default => throw new \RuntimeException(
				'Service token was requested, but oauth2/serviceTokenHandler was not set to a supported value (db | file).'
			),
		};
	}

}
