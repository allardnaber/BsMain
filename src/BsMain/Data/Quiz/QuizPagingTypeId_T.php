<?php

namespace BsMain\Data\Quiz;

enum QuizPagingTypeId_T: int {

	/**
	 * All questions on one page
	 */
	case BreakNever = 0;

	/**
	 * One question per page
	 */
	case BreakAfterQuestion = 1;

	/**
	 * Page breaks after each section
	 */
	case BreakAfterSection = 2;

	/**
	 * Five questions per page
	 */
	case BreakAfter5Questions = 3;

	/**
	 * Ten questions per page
	 */
	case BreakAfter10Questions = 4;

}
