<?php

namespace BsMain\Data\Rubric;

enum Scoring_M: int {
	case TextOnly = 0;
	case Points = 1;
	case TextAndNumeric = 2;
	case CustomPoints = 3;
}
