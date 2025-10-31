<?php

namespace BsMain\Data\Quiz;

/**
 * @see https://docs.valence.desire2learn.com/res/quiz.html#term-LATESUBMISSIONOPTION_T
 */
enum LateSubmissionOption_T: int {

	case AllowNormalSubmission = 0;
	//case UseLateLimit = 1; // As of LE API v1.71, this type value is no longer supported or used.
	case AutoSubmitAttempt = 2;

}
