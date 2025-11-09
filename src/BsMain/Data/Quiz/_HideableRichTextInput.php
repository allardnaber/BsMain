<?php

namespace BsMain\Data\Quiz;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;
use BsMain\Data\RichTextInput;

/**
 * Helper class for fields that have a {@see RichTextInput} and a `IsDisplayed` component.
 */
class _HideableRichTextInput extends ApiEntity {

	public RichTextInput $Text;
	public bool $IsDisplayed;

}
