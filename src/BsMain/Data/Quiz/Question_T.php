<?php

namespace BsMain\Data\Quiz;

/**
 * @see https://docs.valence.desire2learn.com/res/quiz.html#term-QUESTION_T
 */
enum Question_T: int {
	case MultipleChoice = 1;
	case TrueFalse = 2;
	case FillInTheBlank = 3;
	case MultiSelect = 4;
	case Matching = 5;
	case Ordering = 6;
	case LongAnswer = 7;
	case ShortAnswer = 8;
	case Likert = 9;
	case ImageInfo = 10;
	case TextInfo = 11;
	case Arithmetic = 12;
	case SignificantFigures = 13;
	case MultiShortAnswer = 14;
}
