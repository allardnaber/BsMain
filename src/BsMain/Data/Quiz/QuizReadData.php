<?php

namespace BsMain\Data\Quiz;

use BsMain\Data\ApiEntity;

/**
 * see https://docs.valence.desire2learn.com/res/quiz.html#Quiz.QuizReadData
 */
class QuizReadData extends ApiEntity {

	public int $QuizId;
	public string $Name;
	public bool $IsActive;
	public int $SortOrder;
	public ?bool $AutoExportToGrades;
	public ?int $GradeItemId;
	public bool $IsAutoSetGraded;
	public HideableRichText $Instructions;
	public HideableRichText $Description;
	public ?string $StartDate;
	public ?string $EndDate;
	public ?string $DueDate;
	public bool $DisplayInCalendar;
	public mixed $AttemptsAllowed; /*": {
	"IsUnlimited": <boolean>,
	"NumberOfAttemptsAllowed": <number>|null
	},*/
	public mixed $LateSubmissionInfo; /*": {
		"LateSubmissionOption": <number:LATESUBMISSIONOPTION_T>,
			"LateLimitMinutes": <number>|null
		},*/
	public mixed $SubmissionTimeLimit; /*": {
	"IsEnforced": <boolean>,
		"ShowClock": <boolean>,
		"TimeLimitValue": <number>
	},*/
	public ?int $SubmissionGracePeriod;
	public ?string $Password;
	public HideableRichText $Header;
	public HideableRichText $Footer;
	public bool $AllowHints;
	public bool $DisableRightClick;
	public bool $DisablePagerAndAlerts;
	public ?string $NotificationEmail;
	public int $CalcTypeId; //": <number:OVERALLGRADECALCULATION_T>,
	public mixed $RestrictIPAddressRange; /*": null|[
		{
			"IPRangeStart": <string>,
			"IPRangeEnd": <string>|null
		}
	],*/
	public ?int $CategoryId;
	public bool $PreventMovingBackwards;
	public bool $Shuffle;
	public ?string $ActivityId;
	public ?bool $AllowOnlyUsersWithSpecialAccess;
	public bool $IsRetakeIncorrectOnly;
	/**
	 * @var QuizPagingTypeId_T|null For quizzes using modern paging methods, contains the number corresponding to the
	 *                              quiz paging type. For quizzes using the classic paging method, this field value will
	 *                              be null.
	 */
	public ?QuizPagingTypeId_T $PagingTypeId;
	public bool $IsSynchronous;
	/**
	 * @var int|null If not null, denotes the percentage of each incorrect question’s point value to be deducted from the final attempt grade.
	 */
	public ?int $DeductionPercentage;
	public bool $HideQuestionPoints;

}
