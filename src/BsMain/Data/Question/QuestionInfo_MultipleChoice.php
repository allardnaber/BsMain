<?php

namespace BsMain\Data\Question;

use BsMain\Api\Fields\Attributes\ArrayOf;

class QuestionInfo_MultipleChoice extends QuestionInfo {
/*
 * {
   "Answers": [
       {
       "PartId": <number:D2LID>,
       "Answer": { <composite:RichText> },
       "AnswerFeedback": { <composite:RichText> },
       "Weight": <number>
       }, ...
   ],
   "Randomize": <boolean>,
   "Enumeration": <number:ENUMERATION_T>
}
 */

	/**
	 * @var QuestionInfo_Answer[]
	 */
	#[ArrayOf(QuestionInfo_Answer::class)]
	public array $Answers;
	public bool $Randomize;
	public int $Enumeration;
}
