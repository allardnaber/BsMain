<?php

namespace BsMain\Data\Question;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\Quiz\Enumeration_T;

/**
 * @see https://docs.valence.desire2learn.com/res/quiz.html#Question.QuestionInfo / Multiple Choice.
 */
class QuestionInfo_MultipleChoice extends QuestionInfo {

	/**
	 * @var QuestionInfo_MC_Answer[]
	 */
	#[ArrayOf(QuestionInfo_MC_Answer::class)]
	public array $Answers;
	public bool $Randomize;
	public Enumeration_T $Enumeration;

}
