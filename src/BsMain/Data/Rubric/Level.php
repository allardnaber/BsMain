<?php

namespace BsMain\Data\Rubric;

use BsMain\Data\ApiEntity;

class Level extends ApiEntity {
	public int $Id;
	public string $Name;
	public ?float $Points;
}
