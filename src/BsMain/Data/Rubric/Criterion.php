<?php

namespace BsMain\Data\Rubric;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;

class Criterion extends ApiEntity {
	public int $Id;
	public string $Name;

	/** @var CriterionCell[] */
	#[ArrayOf(CriterionCell::class)]
	public array $Cells;
}
