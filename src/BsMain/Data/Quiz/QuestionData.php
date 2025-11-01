<?php

namespace BsMain\Data\Quiz;

use BsMain\Api\Fields\Attributes\CustomMapper;
use BsMain\Data\ApiEntity;
use BsMain\Data\Question\QuestionInfo;
use BsMain\Data\Question\QuestionInfoMapper;
use BsMain\Data\RichText;

/**
 * see https://docs.valence.desire2learn.com/res/quiz.html#Quiz.QuestionData
 */
class QuestionData extends ApiEntity {

	public int $QuestionId;
	public Question_T $QuestionTypeId;
	public ?string $Name;
	public RichText $QuestionText;
	public float $Points; // not documented that it can be decimal
	public int $Difficulty;
	public bool $Bonus;
	public bool $Mandatory;
	public ?RichText $Hint; // not documented that it can be null
	public ?RichText $Feedback; // not documented that it can be null
	public string $LastModified; // @todo datetime
	public ?int $LastModifiedBy;
	public int $SectionId;
	public int $QuestionTemplateId;
	public int $QuestionTemplateVersionId;

	#[CustomMapper(QuestionInfoMapper::class)]
	public QuestionInfo $QuestionInfo;
}
