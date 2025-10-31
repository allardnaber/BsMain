<?php

namespace BsMain\Data\Question;

use BsMain\Data\Quiz\Enumeration_T;
use BsMain\Data\Quiz\Style_T;

class QuestionInfo_MultiSelect extends QuestionInfo {

	public array $Answers; // @todo
	public bool $Randomize;
	public Enumeration_T $Enumeration;
	public Style_T $Style;
	public int $GradingType; //@todo

}
