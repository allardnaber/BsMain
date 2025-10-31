<?php

namespace BsMain\Data\Question;

use BsMain\Data\RichText;

class QuestionInfo_LongAnswer extends QuestionInfo {

	public int $PartId;
	public bool $EnableStudentEditor;
	public RichText $InitialText;
	public RichText $AnswerKey;
	public bool $EnableAttachments;

}
