<?php

namespace BsMain\Db;

use BsMain\Configuration\Configuration;
use BsMain\Exception\InvalidDbObjectException;
use BsMain\Exception\NotFoundException;

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
			$config->getOptional('db', 'pdo_options'),
			$config->getOptional('db', 'initial_queries')
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
	 * @throws NotFoundException
	 */
	public function getById(string $classname, mixed $id): mixed {
		$meta = $this->getTableMetadata($classname);
		return $this->getByFields($classname, [ $meta->getIdColumnName() => $id]);
	}

	/**
	 * @throws NotFoundException
	 * @throws InvalidDbObjectException
	 */
	public function getByFields(string $classname, array $fields): mixed {
		$result = $this->getAllByFields($classname, $fields);
		if (count($result) === 0) {
			throw new NotFoundException(sprintf('Specified item of type %s does not exist', $classname));
		} else {
			return $result[0];
		}
	}

	/**
	 * @throws InvalidDbObjectException
	 */
	public function getAllByFields(string $classname, array $fields = []): array {
		$meta = $this->getTableMetadata($classname);
		$selectFields = [];
		foreach (array_keys($fields) as $key) {
			if ($fields[$key] instanceof DbExpression) {
				$selectFields[] = $key . '=(' . $fields[$key]->get() . ')';
				unset($fields[$key]);
			} else {
				$selectFields[] = $key . '=:' . $key;
			}
		}

		$stmt = $this->prepare(sprintf('select * from %s where %s', $meta->getTableName(), join (' and ', $selectFields)));
		foreach ($fields as $key => $value) {
			$stmt->bindValue($key, $value);
		}

		$stmt->execute();
		if (($dbResult = $stmt->fetchAll(\PDO::FETCH_ASSOC)) === false) {
			return [];
		}
		$result = [];
		foreach ($dbResult as $record) {
			$result[] = new $classname($this, $record);
		}

		return $result;
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