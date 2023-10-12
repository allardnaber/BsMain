<?php

namespace BsMain\Db\Attributes;

use PDO;

#[\Attribute(\Attribute::TARGET_CLASS)]
class IdColumn {
	public function __construct(string $field, int $type = PDO::PARAM_INT) {}
}
