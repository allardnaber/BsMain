<?php

namespace BsMain\Data\Quiz;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

/**
 * Helper class for fields that have a {@see RichText} and a `IsDisplayed` component.
 */
class HideableRichText extends ApiEntity {

	public RichText $Text;
	public bool $IsDisplayed;

}
