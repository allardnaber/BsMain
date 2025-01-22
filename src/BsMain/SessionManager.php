<?php

namespace BsMain;

use BsMain\Configuration\Configuration;

class SessionManager {

	private static self $instance;

	public static function create(Configuration $config): void {
		if (isset(self::$instance)) return;
		self::$instance = new self($config);
		session_start();
	}

	public static function getInstance(): ?self {
		return self::$instance ?? null;
	}

	private function __construct(Configuration $config) {
		$sessionPath = $config->getOptional('app', 'sessionPath');
		if ($sessionPath !== null) {
			session_save_path($sessionPath);

		}
		switch ($config->getOptional('app', 'sessionHandler')) {
			case 'db':
				//echo 'enabling db sessions';

				$dbConfig = $config->getOptional('app', 'sessionDb') ?? $config->get('db');
				//print_r($dbConfig);
				\DbSession\Handler::connectAndRegister($dbConfig['dsn'], $dbConfig['username'], $dbConfig['password'], $dbConfig['pdo_options'] ?? null);
				break;

			default:
				if ($sessionPath !== null) {
					if (!file_exists($sessionPath)) {
						mkdir($sessionPath, 0770, true);
					}
				}
		}
	}
}