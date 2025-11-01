<?php

namespace BsMain\Data\Question;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

class QuestionInfo_MC_Answer extends ApiEntity {
	public int $PartId;
	public RichText $Answer;
	public RichText $AnswerFeedback;
	public float $Weight; //@todo float or int?
}