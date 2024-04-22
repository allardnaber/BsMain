<?php

namespace BsMain\Data;

/**
 * Adds the following fields to ContentObjectData.
 * See https://docs.valence.desire2learn.com/res/content.html#Content.ContentObjectData
Module
{
    "Title": <string>,
    "ShortTitle": <string>,
    "Type": 0,
    "ModuleStartDate": <string:UTCDateTime>|null,
    "ModuleEndDate": <string:UTCDateTime>|null,
    "ModuleDueDate": <string:UTCDateTime>|null,
    "IsHidden": <boolean>,
    "IsLocked": <boolean>,
    "Description": { <composite:RichTextInput> }|null,
    "Duration": <number>|null  // Available in LE's unstable contract
} */

class ContentObjectDataModule extends ContentObjectData {

	protected function getAvailableFields(): array {
		$base = parent::getAvailableFields();
		return array_merge($base, [ 'ModuleStartDate', 'ModuleEndDate', 'ModuleDueDate' ]);
	}
}
