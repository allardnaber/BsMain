<?php

namespace BsMain\Data\Quiz;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

/**
 * see https://docs.valence.desire2learn.com/res/quiz.html#Quiz.QuizAttemptData
 */
class QuizAttemptData extends ApiEntity {

	public int $AttemptId;
	public int $QuizId;
	public int $UserId;
	public int $AttemptNumber;
	public ?float $Score;
	public string $Started;
	public ?string $Completed;
	public RichText $AttemptFeedback;
	public ?string $FeedbackLastModified;
	public bool $IsPublished;
	public bool $IsRetakeIncorrectOnly;
	public ?string $AttemptDueDate;
	public bool $AttemptEnforceTimeLimit;
	public int $AttemptSubmissionTimeLimit;
	public int $AttemptSubmissionGraceLimit;
	public LateSubmissionOption_T $AttemptSubmissionLateTypeId;
	/**
	 * @var int Specifies the extended deadline (in minutes) for this attempt.
	 */
	public int $AttemptSubmissionLateData;
	public bool $AttemptIsSynchronous;
	public ?int $DeductionPercentage;

}
