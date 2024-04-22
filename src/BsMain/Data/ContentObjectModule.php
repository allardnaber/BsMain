<?php

namespace BsMain\Data;

/**
 * ContentObjectModule, see https://docs.valence.desire2learn.com/res/content.html#Content.ContentObject
 *
 * MODULE {
 * "Structure": [ <Content.ContentObject>, ... ],
 * "ModuleStartDate": <string:UTCDateTime>|null,
 * "ModuleEndDate": <string:UTCDateTime>|null,
 * "ModuleDueDate": <string:UTCDateTime>|null,
 * "IsHidden": <boolean>,
 * "IsLocked": <boolean>,
 * "Id": <number:D2LID>,
 * "Title": <string>,
 * "ShortTitle": <string>,
 * "Type": 0,
 * "Description": { <composite:RichText> }|null,
 * "ParentModuleId": <number:D2LID>|null,
 * "Duration": <number>|null,
 * "LastModifiedDate": <string:UTCDateTime>|null
 * }
 */
class ContentObjectModule extends ContentObject {

	protected function getAvailableFields(): array {
		$base = parent::getAvailableFields();
		return array_merge($base, [ 'Structure', 'ModuleStartDate', 'ModuleEndDate', 'ModuleDueDate' ]);
	}
}
