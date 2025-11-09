<?php

namespace BsMain\Data\Quiz;

use BsMain\Api\ApiRequest;
use BsMain\Data\ApiEntity;

/**
 * see https://docs.valence.desire2learn.com/res/quiz.html#Quiz.QuizReadData
 */
class QuizReadData extends _QuizData_Base {

	public int $QuizId;

	public _HideableRichText $Instructions;
	public _HideableRichText $Description;

	public mixed $AttemptsAllowed; /*": {
	"IsUnlimited": <boolean>,
	"NumberOfAttemptsAllowed": <number>|null
	},*/

	public _HideableRichText $Header;
	public _HideableRichText $Footer;

	public ?string $ActivityId;



}
