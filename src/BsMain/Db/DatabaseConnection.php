<?php

namespace BsMain\Db;

use BsMain\Configuration\Configuration;
use BsMain\Exception\InvalidDbObjectException;

class DatabaseConnection extends \PDO {

	/**
	 * @var TableMetadata[]
	 */
	private array $tableMap = [];

	public static function get(Configuration $config): self {
		return new self(
			$config->get('db', 'dsn'),
			$config->get('db', 'username'),
			$config->get('db', 'password'),
			$config->get('db', 'pdo_options')
		);
	}

	public function __construct(string $dsn, string $username, string $password, ?array $options = null, ?array $initQueries = null) {
		parent::__construct($dsn, $username, $password, $options);
		foreach ($initQueries as $query) {
			$this->exec($query);
		}
	}

	/**
	 * Gets an object of certain type by its id.
	 * @param string $classname The classname of the object type to retrieve.
	 * @param mixed $id The id in the type defined in the class.
	 * @return mixed The resulting object.
	 * @throws InvalidDbObjectException If the specified classname does not exist.
	 */
	public function getById(string $classname, mixed $id): mixed {
		$meta = $this->getTableMetadata($classname);
		$stmt = $this->prepare(sprintf('select * from %s where %s = :id limit 1', $meta->getTableName(), $meta->getIdColumnName()));
		$stmt->bindValue('id', $id, $meta->getIdColumnType());
		$stmt->execute();
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);
		return new $classname($this, $result);
	}

	/**
	 * Retrieves all metadata for the specified object type
	 * @param string $classname Classname for which to retrieve metadata
	 * @return TableMetadata The metadata based on class and method attributes
	 * @throws InvalidDbObjectException If the specified class does not exist.
	 */
	public function getTableMetadata(string $classname): TableMetadata {
		if (!isset($this->tableMap[$classname])) {
			$this->tableMap[$classname] = new TableMetadata($classname);
		}
		return $this->tableMap[$classname];
	}


}