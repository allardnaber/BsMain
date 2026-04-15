<?php

namespace BsMain\Configuration;

use Psr\Cache\InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Throwable;
use Vault\Exceptions\RuntimeException;

class Configuration {

	private ?Vault $vault = null;

	private array $config;

	private const CACHE_FALLBACK = -1;
	private const string ENV_CONFIG_PREFIX = 'CONFIG';
	private const string ENV_CONFIG_SEPARATOR = '_';

	public function __construct(array $config) {
		$this->config = $config;
		try {
			$this->resolveConfig();
		} catch (Throwable $e) {
			// @todo LOG
			$stderr = fopen('php://stderr', 'w');
			fprintf($stderr, "Unable to resolve config: %s: %s\n", $e->getMessage(), $e->getTraceAsString());
			fclose($stderr);
		}
	}

	public function getOptional(string ...$path): string|array|null {
		return $this->getConfigVariable(true, ...$path);
	}

	public function get(string ...$path): string|array {
		return $this->getConfigVariable(false, ...$path);
	}

	private function getConfigVariable(bool $optional, string ...$path): string|array|null {
		$configSection = $this->config;
		foreach ($path as $pathItem) {
			if (!isset ($configSection[$pathItem])) {
				if ($optional) {
					return null;
				} else {
					$var = join('/', $path);
					throw new \RuntimeException(sprintf('Required config variable %s does not exist.', $var));
				}
			}
			$configSection = $configSection[$pathItem];
		}

		return $configSection;
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

	/**
	 * @throws Throwable
	 * @throws ClientExceptionInterface
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	private function resolveConfig(): void {
		if (($fromCache = $this->getFromCache(-1)) !== null) {
			$this->config = $fromCache;
			return;
		}

		try {
			// Resolve potential environment variable overrides
			$this->resolveEnvOverrides($this->config);

			// Initialize vault if required
			if (count(array_intersect(
					['vaultUri', 'vaultToken', 'vaultPath'],
					array_keys($this->config['config']))) === 3) {
				$this->initVault();
			}
			$this->resolveSecrets($this->config);
			$this->saveToCache();
		} catch (Throwable $e) {
			// Fall back to old cache if it's impossible to renew.
			$stderr = fopen('php://stderr', 'w');
			fprintf($stderr, "Unable to reload config: %s: %s\n", $e->getMessage(), $e->getTraceAsString());
			fclose($stderr);
			$oldCache = $this->getFromCache(self::CACHE_FALLBACK);
			if ($oldCache !== null) {
				$this->config = $oldCache;
			} else{
				throw $e;
			}
		}
	}

	/**
	 * @throws ClientExceptionInterface
	 */
	private function resolveSecrets(&$part): void {
		if (is_array($part)) {
			foreach ($part as &$subPart) {
				$this->resolveSecrets($subPart);
			}
		} elseif ($part instanceof VaultSecret) {
			$part = $this->vault->getSecret($part->getKey());
		}
		// else string: keep as is.
	}

	private function resolveEnvOverrides(&$part, array $path = []): void {
		if (is_array($part)) {
			foreach ($part as $key => &$subPart) {
				$this->resolveEnvOverrides($subPart, [ ...$path, $key] );
			}
		} else {
			// value can be overridden by environment variables
			$envName = strtoupper(join(self::ENV_CONFIG_SEPARATOR, [ self::ENV_CONFIG_PREFIX, ...$path ]));
			if (($value = getenv($envName)) !== false) {
				$part = $this->parseEnvValue($value);
			}
		}
	}

	private function parseEnvValue(string $value): mixed {
		if (preg_match('^(?:\\[.*\\]|{.*})$', $value)) {
			return json_decode($value);
		} else {
			return $value;
		}
	}

	private function getFromCache(int $maxAge): ?array {
		$fname = $this->config['config']['cachePath'];
		if (file_exists($fname) && ($maxAge === self::CACHE_FALLBACK || time() - filemtime($fname) < $maxAge)) {
			if ($maxAge === self::CACHE_FALLBACK) {
				touch($fname); // try again after the next interval
			}
			return unserialize(file_get_contents($fname));
		}
		return null;
	}

	private function saveToCache(): void {
		$fname = $this->config['config']['cachePath'];
		file_put_contents($fname, serialize($this->config));
	}
}
