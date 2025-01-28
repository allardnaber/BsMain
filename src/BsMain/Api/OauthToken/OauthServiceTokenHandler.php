<?php

namespace BsMain\Api\OauthToken;

use BsMain\Configuration\Configuration;
use BsMain\Exception\BsAppRuntimeException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class OauthServiceTokenHandler extends OauthTokenHandler {

	public abstract function setServiceToken(AccessTokenInterface $serviceToken): void;

	public static function get(AbstractProvider $provider, Configuration $config): self {
		return match ($config->getOptional('oauth2', 'serviceTokenHandler')) {
			'db' => new OauthDatabaseServiceTokenHandler($provider, $config),
			'file' => new OauthFileServiceTokenHandler($provider, $config),
			default => throw new BsAppRuntimeException(
				'Service token was requested, but oauth2/serviceTokenHandler was not set to a supported value (db | file).'
			),
		};
	}

}
