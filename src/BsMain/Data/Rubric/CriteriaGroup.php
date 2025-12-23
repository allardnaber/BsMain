<?php

namespace BsMain\Data\Rubric;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;

class CriteriaGroup extends ApiEntity {

	public string $Name;
	/** @var Level[] */
	#[ArrayOf(Level::class)]
	public array $Levels;

	/** @var Criterion[] */
	#[ArrayOf(Criterion::class)]
	public array $Criteria;
}
