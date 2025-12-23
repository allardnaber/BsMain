<?php

namespace BsMain\Data\Rubric;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

class OverallLevel extends ApiEntity {
	public int $Id;
	public string $Name;
	public ?float $RangeStart;
	public RichText $Description;
	public RichText $Feedback;
}
