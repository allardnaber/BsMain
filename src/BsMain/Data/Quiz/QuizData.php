<?php

namespace BsMain\Data\Quiz;

class QuizData extends _QuizData_Base {

	public _HideableRichTextInput $Instructions;
	public _HideableRichTextInput $Description;
	public _HideableRichTextInput $Header;
	public _HideableRichTextInput $Footer;
	public ?int $NumberOfAttemptsAllowed;

}
