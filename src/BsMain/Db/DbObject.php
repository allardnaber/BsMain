<?php

namespace BsMain\Db;

use BsMain\Exception\InvalidDbObjectException;

abstract class DbObject {

	/**
	 * @var string[] Updated fields since last save.
	 */
	private array $dirty = [];

	/**
	 * @var array  Field contents
	 */
	private array $fields = [];

	private DatabaseConnection $connection;

	public function __construct(DatabaseConnection $connection, array $fields = []) {
		$this->connection = $connection;
		foreach ($fields as $key => $value) {
			$this->fields[$key] = $value;
		}
	}

	public function __set(string $name, mixed $value): void {
		if (!isset($this->fields[$name]) || $this->fields[$name] !== $value) {
			$this->fields[$name] = $value;
			$this->dirty[] = $name;
		}
	}

	public function __get(string $name): mixed {
		return $this->fields[$name] ?? null;
	}

	public function save(): void {
		if (count($this->dirty) > 0) {
			$meta = $this->getOwnMetadata();
			$updateFields = [];
			foreach ($this->fields as $key => $value) {
				if ($value instanceof DbExpression) {
					$updateFields[] = $key . '=(' . $value->get() . ')';
					unset ($this->fields[$key]);
				} else {
					$updateFields[] = $key . '=:' . $key;
				}
			}

			$stmt = $this->connection->prepare(sprintf(
				'update %1$s set %2$s where %3$s=:%3$s',
				$meta->getTableName(),
				join(', ', $updateFields),
				$meta->getIdColumnName())
			);

			foreach ($this->fields as $key=>$value) {
				$stmt->bindValue($key, $value);
			}
			if ($stmt->execute()) {
				$this->dirty = [];
			}
		}
	}

	/** @noinspection PhpUnhandledExceptionInspection */
	private function getOwnMetadata(): TableMetadata {
		return $this->connection->getTableMetadata($this::class);
	}

}
