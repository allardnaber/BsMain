<?php

namespace BsMain\Data\Question;

use BsMain\Data\ApiEntity;

class QuestionInfo_Answer extends ApiEntity {
/**
 * {
 * "PartId": <number:D2LID>,
 * "Answer": { <composite:RichText> },
 * "AnswerFeedback": { <composite:RichText> },
 * "Weight": <number>
 * }, ...
 */
	public int $PartId;
	public mixed $Answer; // RichtText
	public mixed $AnswerFeedback; // RichtText
	public float $Weight; //@todo float or int?
}