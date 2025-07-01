<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/quiz.html#Quiz.QuizAttemptData
 * {
"AttemptId": <number:D2LID>,
"QuizId": <number:D2LID>,
"UserId": <number:D2LID>,
"AttemptNumber": <number>,
"Score": <number:decimal>|null,
"Started": <string:UTCDateTime>,
"Completed": <string:UTCDateTime>|null,
"AttemptFeedback": { <composite:RichText> },
"FeedbackLastModified": <string:UTCDateTime>|null,
"IsPublished":  <boolean>,
"IsRetakeIncorrectOnly": <boolean>,
"AttemptDueDate": <string:UTCDateTime>|null,  // Added with LE API v1.54
"AttemptEnforceTimeLimit": <boolean>,  // Added with LE API v1.54
"AttemptSubmissionTimeLimit": <number>,  // Added with LE API v1.54
"AttemptSubmissionGraceLimit": <number>,  // Added with LE API v1.54
"AttemptSubmissionLateTypeId": <number>,  // Added with LE API v1.54
"AttemptSubmissionLateData": <number>  // Added with LE API v1.54
}
 */
class QuizAttemptData extends GenericObject {

	protected function getAvailableFields(): array {
		return [
			'AttemptId', 'QuizId', 'UserId', 'AttemptNumber', 'Started', 'Completed'
		];
	}
}
