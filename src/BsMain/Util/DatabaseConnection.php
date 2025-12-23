<?php

namespace BsMain\Util;

use PDO;
use Sald\Connection\Configuration;
use Sald\Sald;

class DatabaseConnection {

	public static function getConnection(array $dbConfig): PDO {

		return Sald::get(new Configuration(
			$dbConfig['dsn'],
			$dbConfig['username'],
			$dbConfig['password'],
			$dbConfig['pdo_options'] ?? null,
			$dbConfig['schema'] ?? null));
	}

}
