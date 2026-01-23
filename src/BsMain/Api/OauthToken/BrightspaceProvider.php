<?php

namespace BsMain\Api\OauthToken;

use League\OAuth2\Client\Provider\GenericProvider;

class BrightspaceProvider extends GenericProvider {

	public function getClientId(): string {
		return $this->clientId;
	}

}
