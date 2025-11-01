<?php

namespace BsMain\Data\Question;

use BsMain\Api\Fields\Attributes\ArrayOf;

/**
 * @see https://docs.valence.desire2learn.com/res/quiz.html#Question.QuestionInfo / Fill In The Blank
 */
class QuestionInfo_FillInTheBlank extends QuestionInfo {

	#[ArrayOf(QuestionInfo_FITB_Text::class)]
	public array $Texts;

	#[ArrayOf(QuestionInfo_FITB_Blank::class)]
	public array $Blanks;
}
