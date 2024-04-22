<?php

namespace BsMain\Data;


use BsMain\Exception\BsAppApiException;
use BsMain\Exception\BsAppRuntimeException;

/**
 * ContentObject is the combined object for modules and topics. Common properties are in this object, more specific
 * properties are in the overloaded classes ContentObjectModule and ContentObjectTopic. The {@see instance}
 * method differentiates between the two.
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
 *
 * TOPIC {
 * "TopicType": <number:TOPIC_T>,
 * "Url": <string>|null,
 * "StartDate": <string:UTCDateTime>|null,
 * "EndDate": <string:UTCDateTime>|null,
 * "DueDate": <string:UTCDateTime>|null,
 * "IsHidden": <boolean>,
 * "IsLocked": <boolean>,
 * "IsBroken": <boolean>,  // Added with LE API 1.72
 * "OpenAsExternalResource": <boolean>|null,
 * "Id": <number:D2LID>,
 * "Title": <string>,
 * "ShortTitle": <string>,
 * "Type": 1,
 * "Description": { <composite:RichText> }|null,
 * "ParentModuleId": <number:D2LID>,
 * "ActivityId": <string>|null,
 * "Duration": <number>|null,  // Available in LE's unstable contract
 * "IsExempt": <boolean>,
 * "ToolId": <number:D2LID>|null,
 * "ToolItemId":  <number:D2LID>|null,
 * "ActivityType": <number:ACTIVITYTYPE_T>,
 * "GradeItemId": <number:D2LID>|null,
 * "LastModifiedDate": <string:UTCDateTime>|null,
 * "AssociatedGradeItemIds": [<number:D2LID>, ...]  // Added with LMS v20.23.3
 * }
 */
class ContentObject extends GenericObject {

	protected function getAvailableFields(): array {
		return [
			'IsHidden', 'IsLocked', 'Id', 'Title', 'ShortTitle', 'Type', 'Description',
			'ParentModuleId', 'LastModifiedDate'
		];
	}

	public static function instance(?array $json = null): static {
		if ($json === null) {
			return parent::instance($json);
		}
		if (!isset($json['Type'])) {
			throw new BsAppRuntimeException('Content Object type is not specified, this is a required field.');
		}
		return match ($json['Type']) {
			Content_T::Module => new ContentObjectModule($json),
			Content_T::Topic => new ContentObjectTopic($json),
			default => throw new BsAppRuntimeException(sprintf('Content object type %d is unknown.', $json['Type'])),
		};
	}
}
