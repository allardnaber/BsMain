<?php

namespace BsMain\Db\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class TableName {
	public function __construct(string $tableName) {}
}
