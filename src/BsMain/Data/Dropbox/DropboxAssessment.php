<?php

namespace BsMain\Data\Dropbox;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;
use BsMain\Data\Rubric\Rubric;

class DropboxAssessment extends ApiEntity {

	public float $ScoreDenominator;
	/** @var Rubric[] */
	#[ArrayOf(Rubric::class)]
	public array $Rubrics;
}
