<?php

namespace BsMain\Controller;

use BsMain\Api\BsApiClient;
use BsMain\Api\OauthToken\OauthClientTokenHandler;
use BsMain\Api\OauthToken\OauthServiceTokenHandler;
use BsMain\Configuration\Configuration;
use BsMain\Controller\Attributes\Route;
use BsMain\Data\WhoAmIUser;
use BsMain\Template\OutputTemplate;
use RuntimeException;

class AccountController extends BsBaseController {

	private BsApiClient $client;
	private bool $isServiceAccount;
	/** @noinspection SpellCheckingInspection */
	private WhoAmIUser $whoami;

	public function __construct(OutputTemplate $output, Configuration $config) {
		parent::__construct($output, $config);
		$this->client = new BsApiClient($this->getFullConfig());

		$this->whoami = $this->client->whoami();

		$this->isServiceAccount = $this->getConfigOptional('brightspace', 'serviceAccount') != null &&
			strtolower($this->whoami->UniqueName) === strtolower($this->getConfig('brightspace', 'serviceAccount'));

		/** @noinspection SpellCheckingInspection */
		$this->assign('whoami', $this->whoami);
		$this->assign('token',  json_encode(OauthClientTokenHandler::getTokenFromSession()->jsonSerialize()));
		$this->assign('isServiceAccount', $this->isServiceAccount);
	}

	#[Route('/account')]
	public function showAccount(): void {
		$this->display('account.tpl');
	}

	#[Route('/account/register')]
	public function registerAccessToken(): void {
		if (!$this->isServiceAccount) {
			throw new RuntimeException(sprintf(
				'Registering a service access token can only be done from the %s account.',
				$this->getConfig('brightspace', 'serviceAccount'))
			);
		}

		OauthServiceTokenHandler::saveAccessToken($this->getFullConfig(), OauthClientTokenHandler::getTokenFromSession());

		$this->assign('tokenRegistered', true);
		$this->display('account.tpl');
	}

}
