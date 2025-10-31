<?php

namespace BsMain\Data\Question;

use BsMain\Api\Fields\FieldMapper;
use BsMain\Data\Quiz\Question_T;

class QuestionInfoMapper extends FieldMapper {

	public function map(array $input): mixed {
		return match (Question_T::from($input['QuestionTypeId'])) {
			Question_T::MultipleChoice => QuestionInfo_MultipleChoice::newInstance($input[$this->name]),
			default => QuestionInfo::newInstance($input[$this->name]),
		};	}
}
