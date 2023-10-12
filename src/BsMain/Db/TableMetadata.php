<?php

namespace BsMain\Db;

use BsMain\Db\Attributes\IdColumn;
use BsMain\Db\Attributes\TableName;
use BsMain\Exception\InvalidDbObjectException;
use PDO;
use ReflectionClass;

class TableMetadata {

	private string $tableName;
	private string $idColumnName = 'id';
	private int $idColumnType = PDO::PARAM_INT;

	/**
	 * @throws InvalidDbObjectException
	 */
	public function __construct($classname = null) {
		try {
			$reflection = new ReflectionClass($classname);
			$this->findTableName($reflection);
			$this->findIdColumn($reflection);

		} catch (\ReflectionException $e) {
			throw new InvalidDbObjectException(
				sprintf('Class %s does not exist and cannot be used as database object', $classname),
				$e->getCode(), $e);
		}
	}

	public function getTableName(): string { return $this->tableName; }
	public function getIdColumnName(): string { return $this->idColumnName; }
	public function getIdColumnType(): int { return $this->idColumnType; }

	private function findTableName(ReflectionClass $reflection): void {
		$this->tableName = $reflection->getShortName(); // use as backup
		$tableNameAttr = $reflection->getAttributes(TableName::class);
		if (count($tableNameAttr) > 0) {
			$arguments = $tableNameAttr[0]->getArguments();
			if (count($arguments) > 0) {
				$this->tableName = $arguments[0];
			}
		}
	}

	private function findIdColumn(ReflectionClass $reflection): void {
		$idColumnAttr = $reflection->getAttributes(IdColumn::class);
		if (count($idColumnAttr) > 0) {
			$arguments = $idColumnAttr[0]->getArguments();
			if (count($arguments) > 0) {
				$this->idColumnName = $arguments[0];
				$this->idColumnType = $arguments[1] ?? PDO::PARAM_INT;
			}
		}
	}
}
