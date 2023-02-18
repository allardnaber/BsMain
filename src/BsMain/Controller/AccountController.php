<?php

namespace BsMain\Controller;

use BsMain\Api\BsApiClient;
use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Controller\Attributes\Route;
use BsMain\Template\OutputTemplate;

class AccountController extends BsBaseController {

	private $client;
	private $isServiceAccount;
	private $whoami;

	public function __construct(OutputTemplate $output, $config) {
		parent::__construct($output, $config);
		$this->client = new BsApiClient($this->getConfig());

		$this->whoami = $this->client->whoami();

		$this->isServiceAccount = isset($this->getConfig()['brightspace']['serviceAccount']) &&
			strtolower($this->whoami->UniqueName) === strtolower($this->getConfig()['brightspace']['serviceAccount']);

		$this->assign('whoami', $this->whoami);
		$this->assign('token',  json_encode(OauthClientTokenHandler::getTokenFromSession()->jsonSerialize()));
		$this->assign('isServiceAccount', $this->isServiceAccount);
	}

	#[Route('/account')]
	public function showAccount() {
		$this->display('account.tpl');
	}

	#[Route('/account/register')]
	public function registerAccessToken() {
		if (!$this->isServiceAccount) {
			throw new \RuntimeException(sprintf(
				'Registering a service access token can only be done from the %s account.',
				$this->getConfig()['brightspace']['serviceAccount'])
			);
		}

		OauthServiceTokenHandler::saveAccessToken(OauthClientTokenHandler::getTokenFromSession(), $this->getConfig());

		$this->assign('tokenRegistered', true);
		$this->display('account.tpl');
	}

}
