<?php

namespace BsMain\Controller;

use BsMain\Api\BsApiClient;
use BsMain\Configuration\Configuration;
use BsMain\Controller\Attributes\Route;
use BsMain\Template\OutputTemplate;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use RuntimeException;
use SmartyException;

class AccountController extends BsBaseController {

	private BsApiClient $client;
	private bool $isServiceAccount;

	public function __construct(OutputTemplate $output, Configuration $config) {
		parent::__construct($output, $config);
		$this->client = new BsApiClient($this->getFullConfig());
	}

	/**
	 * @throws IdentityProviderException
	 */
	private function assignSessionVars(): void {
		/** @noinspection SpellCheckingInspection */
		$whoami = $this->client->whoami();

		$this->isServiceAccount = $this->getConfigOptional('brightspace', 'serviceAccount') != null &&
			strtolower($whoami->UniqueName) === strtolower($this->getConfig('brightspace', 'serviceAccount'));

		/** @noinspection SpellCheckingInspection */
		$this->assign('whoami', $whoami);
		$this->assign('token',  json_encode($this->client->getTokenHandler()->getAccessToken()->jsonSerialize()));
		$this->assign('isServiceAccount', $this->isServiceAccount);
	}

	/**
	 * @throws SmartyException
	 * @throws IdentityProviderException
	 */
	#[Route('/account')]
	public function showAccount(): void {
		$this->assignSessionVars();
		$this->display('account.tpl');
	}

	/**
	 * @throws SmartyException
	 * @throws IdentityProviderException
	 */
	#[Route('/account/register')]
	public function registerAccessToken(): void {
		$this->assignSessionVars();

		if (!$this->isServiceAccount) {
			throw new RuntimeException(sprintf(
				'Registering a service access token can only be done from the account with username %s.',
				$this->getConfig('brightspace', 'serviceAccount'))
			);
		}

		$serviceApiClient = new BsApiClient($this->getFullConfig(), true);
		$serviceApiClient->registerServiceToken($this->client->getTokenHandler()->getAccessToken());

		$this->assign('tokenRegistered', true);
		$this->display('account.tpl');
	}

}
