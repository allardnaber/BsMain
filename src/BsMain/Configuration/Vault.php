<?php

namespace BsMain\Configuration;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Uri;
use Psr\Cache\InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Vault\AuthenticationStrategies\TokenAuthenticationStrategy;
use Vault\Client;
use Vault\Exceptions\RuntimeException;

class Vault {

	private Client $client;
	private string $path;
	private array $secrets;

	/**
	 * @throws ClientExceptionInterface
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function __construct(string $uri, string $token, string $path) {
		$this->path = $path;
		$factory = new HttpFactory();
		$this->client = new VaultDebugClient(new Uri($uri), new \GuzzleHttp\Client(), $factory, $factory);
		if (!$this->client->setAuthenticationStrategy(new TokenAuthenticationStrategy($token))->authenticate()) {
			throw new \RuntimeException('Could not access Vault to retrieve required tokens.');
		}
		$this->renewToken();
	}

	/**
	 * Renews lease term of Vault token. The same token will be valid for another 30 days.
	 */
	private function renewToken(): void {
		try {
			$this->client->post($this->client->buildPath('/auth/token/renew-self'));
		} catch (ClientExceptionInterface $e) {
			// proceed @todo log
		}
	}

	/**
	 * @throws ClientExceptionInterface
	 */
	public function getSecret(string $key): string {
		if (!isset($this->secrets)) {
			$response = $this->client->read($this->path);
			$this->secrets = $response->getData();
			if (isset($this->secrets['data']) && isset($this->secrets['metadata'])) { // V2 response
				$this->secrets = $this->secrets['data'];
			}
		}
		if (!isset($this->secrets[$key])) {
			return new \RuntimeException(sprintf('Secret %s not found in Vault.', $key));
		}

		return $this->secrets[$key];
	}

	public static function secret(string $key): VaultSecret {
		return new VaultSecret($key);
	}

}
