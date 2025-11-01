<?php

namespace BsMain\Data\Question;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;

class QuestionInfo_FITB_Blank extends ApiEntity {

	public int $PartId;
	public int $Size;

	#[ArrayOf(QuestionInfo_FITB_BlankAnswer::class)]
	public array $Answers;
}
