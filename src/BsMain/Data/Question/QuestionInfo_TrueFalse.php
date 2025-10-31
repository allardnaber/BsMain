<?php

namespace BsMain\Data\Question;

use BsMain\Data\Quiz\Enumeration_T;
use BsMain\Data\RichText;

/**
 * @see https://docs.valence.desire2learn.com/res/quiz.html#Question.QuestionInfo / True-False
 */
class QuestionInfo_TrueFalse extends QuestionInfo {

	public int $TruePartId;
	public int $TrueWeight;
	public RichText $TrueFeedback;
	public int $FalsePartId;
	public int $FalseWeight;
	public RichText $FalseFeedback;
	public Enumeration_T $Enumeration;

}
