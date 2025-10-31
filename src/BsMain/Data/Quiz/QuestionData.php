<?php

namespace BsMain\Data\Quiz;

use BsMain\Api\Fields\Attributes\CustomMapper;
use BsMain\Data\ApiEntity;
use BsMain\Data\Question\QuestionInfo;
use BsMain\Data\Question\QuestionInfoMapper;

class QuestionData extends ApiEntity {

	/**
	 * {
	 * "QuestionId": <number:D2LID>,
	 * "QuestionTypeId": <number:QUESTION_T>,
	 * "Name": <string>|null,
	 * "QuestionText": { <composite:RichText> },
	 * "Points": <number>,
	 * "Difficulty": <number>,
	 * "Bonus": <boolean>,
	 * "Mandatory": <boolean>,
	 * "Hint": { <composite:RichText> },
	 * "Feedback": { <composite:RichText> },
	 * "LastModified": <utcdatetime>,
	 * "LastModifiedBy": <number:D2LID>|null,
	 * "SectionId": <number:D2LID>,
	 * "QuestionTemplateId": <number:D2LID>,
	 * "QuestionTemplateVersionId": <number:D2LID>,
	 * "QuestionInfo": { <composite:QuestionInfo> }
	 * }
	 */
	public int $QuestionId;
	public Question_T $QuestionTypeId;

	#[CustomMapper(QuestionInfoMapper::class)]
	public QuestionInfo $QuestionInfo;
}
