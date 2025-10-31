<?php

namespace BsMain\Data\Quiz;

use BsMain\Api\Resource\ApiShell;
use BsMain\Data\RichText;

/**
 * Helper class for fields that have a {@see RichText} and a `IsDisplayed` component.
 */
class HideableRichText extends ApiShell {

	public RichText $Text;
	public bool $IsDisplayed;

}
