<?php

namespace BsMain\Data\Content;

use BsMain\Data\ApiEntity;
use BsMain\Data\Content_T;
use BsMain\Data\RichTextInput;

abstract class ContentObjectData extends ApiEntity {

	public ?RichTextInput $Description;
	public string $Title;
	public string $ShortTitle;
	public Content_T $Type;
	public bool $IsHidden;
	public bool $IsLocked;
	// public ?int $Duration // Available in LE's unstable contract

}
