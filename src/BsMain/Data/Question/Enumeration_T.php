<?php

namespace BsMain\Data\Question;

/**
 * @see https://docs.valence.desire2learn.com/res/quiz.html#term-ENUMERATION_T
 */
enum Enumeration_T: int {

	case Numbers = 1;
	case Roman = 2;
	case UpperCaseRoman = 3;
	case Letters = 4;
	case UpperCaseLetters = 5;
	case None = 6;

}
