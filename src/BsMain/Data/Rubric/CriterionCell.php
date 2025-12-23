<?php

namespace BsMain\Data\Rubric;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

class CriterionCell extends ApiEntity {
	public RichText $Feedback;
	public RichText $Description;
	public ?float $Points;
}
