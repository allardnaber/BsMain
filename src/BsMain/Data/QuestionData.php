<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/quiz.html#Quiz.QuizReadData
 * {
"QuizId": <number:D2LID>,
"Name": <string>,
"IsActive": <boolean>,
"SortOrder": <number>,
"AutoExportToGrades": <boolean>|null,
"GradeItemId": <number:D2LID>|null,
"IsAutoSetGraded": <boolean>,
"Instructions": {
"Text": { <composite:RichText> },
"IsDisplayed": <boolean>
},
"Description": {
"Text": { <composite:RichText> },
"IsDisplayed": <boolean>
},
"StartDate": <string:UTCDateTime>|null,
"EndDate": <string:UTCDateTime>|null,
"DueDate": <string:UTCDateTime>|null,
"DisplayInCalendar": <boolean>,
"AttemptsAllowed": {
"IsUnlimited": <boolean>,
"NumberOfAttemptsAllowed": <number>|null
},
"LateSubmissionInfo": {
"LateSubmissionOption": <number:LATESUBMISSIONOPTION_T>,
"LateLimitMinutes": <number>|null
},
"SubmissionTimeLimit": {
"IsEnforced": <boolean>,
"ShowClock": <boolean>,
"TimeLimitValue": <number>
},
"SubmissionGracePeriod": <number>|null,
"Password": <string>,
"Header": {
"Text": { <composite:RichText> },
"IsDisplayed": <boolean>
},
"Footer": {
"Text": { <composite:RichText> },
"IsDisplayed": <boolean>
},
"AllowHints": <boolean>,
"DisableRightClick": <boolean>,
"DisablePagerAndAlerts": <boolean>,
"NotificationEmail": <string>,
"CalcTypeId": <number:OVERALLGRADECALCULATION_T>,
"RestrictIPAddressRange": [
{
"IPRangeStart": <string>,
"IPRangeEnd": <string>|null
}
],
"CategoryId": <number>|null,
"PreventMovingBackwards": <boolean>,
"Shuffle": <boolean>,
"ActivityId": <string>|null,
"AllowOnlyUsersWithSpecialAccess": <boolean>,
"IsRetakeIncorrectOnly": <boolean>
}
 */
class QuestionData extends GenericObject {

	protected function getAvailableFields(): array {
		return [
			'QuestionId', 'QuestionTypeId', 'Name', 'QuestionText', 'Points', 'QuestionInfo', 'Hint', 'SectionId'
		];
	}
}


