<?php

namespace BsMain\Db;

use BsMain\Configuration\Configuration;
use BsMain\Exception\InvalidDbObjectException;
use BsMain\Exception\NotFoundException;
use RuntimeException;

class DatabaseConnection extends \PDO {

	/**
	 * @var TableMetadata[]
	 */
	private array $tableMap = [];

	private Configuration $config;

	public static function get(Configuration $config): self {
		return new self($config);
	}

	public function __construct(Configuration $config) {
		parent::__construct($config->get('db', 'dsn'),
			$config->get('db', 'username'),
			$config->get('db', 'password'),
			$config->getOptional('db', 'pdo_options'));
		$this->config = $config;
		foreach ($config->getOptional('db', 'initial_queries') ?? [] as $query) {
			$this->exec($query);
		}
		$this->tryVersionUpgrade();
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
		if (empty($selectFields)) {
			$selectFields = [ '1=1' ];
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

	private function tryVersionUpgrade(): void {
		$path = $this->config->get('db', 'definition');

		$stmt = $this->prepare('select version from meta limit 1');
		$version = 0;
		try {
			$stmt->execute();
			if (($result = $stmt->fetch()) !== false) {
				$version = $result['version'];;
			}
		} catch(\Exception $e) {
			// ignore if table does not exist (yet)
		}

		if ($version < $this->config->get('db', 'version')) {
			if (($files = scandir($path)) === false) {
				throw new RuntimeException(sprintf('Database definition files in %s cannot be found.', $path));
			}
			$versions = [];
			foreach ($files as $file) {
				if (!preg_match('/v([0-9]+)[._]/i', $file, $m)) {
					continue;
				}
				$versions[$m[1]] = $file;
			}

			$dest = $this->config->get('db', 'version');
			for ($v = $version + 1; $v <= $dest; $v++) {
				if (!isset($versions[$v])) {
					throw new RuntimeException(sprintf('No database definition file for version %d found.', $v));
				}

				$filename = $path . DIRECTORY_SEPARATOR . $versions[$v];
				if (!is_readable($filename)) {
					throw new RuntimeException(sprintf('Database definition file %s does not exist or is not readable.', $filename));
				}
				$stmt = $this->prepare(file_get_contents($path . DIRECTORY_SEPARATOR . $versions[$v]));
				$stmt->execute();
			}

			$stmt = $this->prepare('update meta set version=:version');
			$stmt->bindValue('version', $dest, \PDO::PARAM_INT);
			$stmt->execute();
		}
	}


}