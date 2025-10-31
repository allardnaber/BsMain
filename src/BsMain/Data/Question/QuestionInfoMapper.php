<?php

namespace BsMain\Data\Question;

use BsMain\Api\Fields\FieldMapper;
use BsMain\Data\Quiz\Question_T;

class QuestionInfoMapper extends FieldMapper {

	public function map(array $input): QuestionInfo {
		return match (Question_T::from($input['QuestionTypeId'])) {
			Question_T::MultipleChoice =>   QuestionInfo_MultipleChoice::newInstance($input[$this->name]),
			Question_T::TrueFalse =>        QuestionInfo_TrueFalse::newInstance($input[$this->name]),
			Question_T::FillInTheBlank =>   QuestionInfo_FillInTheBlank::newInstance($input[$this->name]),
			Question_T::MultiSelect =>      QuestionInfo_MultiSelect::newInstance($input[$this->name]),
			Question_T::LongAnswer =>       QuestionInfo_LongAnswer::newInstance($input[$this->name]),
			Question_T::ShortAnswer =>      QuestionInfo_ShortAnswer::newInstance($input[$this->name]),
			Question_T::Likert =>           QuestionInfo_Likert::newInstance($input[$this->name]),
			Question_T::MultiShortAnswer => QuestionInfo_MultiShortAnswer::newInstance($input[$this->name]),
			default => QuestionInfo::newInstance($input[$this->name]),
		};
	}
}
