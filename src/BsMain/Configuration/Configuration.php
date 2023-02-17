<?php

namespace BsMain\Configuration;

use Psr\Cache\InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Vault\Exceptions\RuntimeException;

class Configuration {

	private array $config;
	private ?Vault $vault = null;

	public function __construct(array $config, bool $skipVault = false) {
		$this->config = $config;
	}

	public function get(string ...$path): string|array {
		$var = join(',', $path);
		$c = $this->config;
		foreach ($path as $pathItem) {
			if (!isset ($c[$pathItem])) {
				throw new \RuntimeException('Required config variable %s does not exist.', $var);
			}
			$c = $c[$pathItem];
		}

		if ($c instanceof VaultSecret) {
			if ($this->vault === null) {
				throw new \RuntimeException('Secret from vault required for config variable %s, but Vault has not been set up.', $var);
			} else {
				return $this->vault->getSecret($c->getKey());
			}
		} else {
			return $c;
		}
	}

	/**
	 * @throws ClientExceptionInterface
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	private function initVault(): void {
		$this->vault = new Vault(
			$this->config['config']['vaultUri'],
			$this->config['config']['vaultToken'],
			$this->config['config']['vaultPath']);
	}

	public function getResolvedConfig(): array {
		$fromCache = $this->getFromCache();
		if ($fromCache !== null) {
			return $fromCache;
		}

		// Initialize vault if required
		if (count(array_intersect(
				['vaultUri', 'vaultToken', 'vaultPath'],
				array_keys($this->config['config']))) === 3) {
			$this->initVault();
		}
		$this->resolve($this->config);

		$this->saveToCache();

		return $this->config;
	}

	private function resolve(&$part): void {
		if (is_array($part)) {
			foreach ($part as &$subPart) {
				$this->resolve($subPart);
			}
		} elseif ($part instanceof VaultSecret) {
			$part = $this->vault->getSecret($part->getKey());
		}
		// else string: keep as is.
	}

	private function getFromCache(): ?array {
		$fname = $this->config['config']['cachePath'];
		if (file_exists($fname) && time() - filemtime($fname) < 60*60*24) {
			return unserialize(file_get_contents($fname));
		}
		return null;
	}

	private function saveToCache(): void {
		$fname = $this->config['config']['cachePath'];
		file_put_contents($fname, serialize($this->config));
	}
}
