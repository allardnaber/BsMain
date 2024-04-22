<?php

namespace BsMain\Data;

/**
 * ContentObjectTopic, see https://docs.valence.desire2learn.com/res/content.html#Content.ContentObject
 *
 * TOPIC {
 * * "TopicType": <number:TOPIC_T>,
 * * "Url": <string>|null,
 * * "StartDate": <string:UTCDateTime>|null,
 * * "EndDate": <string:UTCDateTime>|null,
 * * "DueDate": <string:UTCDateTime>|null,
 * * "IsHidden": <boolean>,
 * * "IsLocked": <boolean>,
 * * "IsBroken": <boolean>,  // Added with LE API 1.72
 * * "OpenAsExternalResource": <boolean>|null,
 * * "Id": <number:D2LID>,
 * * "Title": <string>,
 * * "ShortTitle": <string>,
 * * "Type": 1,
 * * "Description": { <composite:RichText> }|null,
 * * "ParentModuleId": <number:D2LID>,
 * * "ActivityId": <string>|null,
 * * "Duration": <number>|null,  // Available in LE's unstable contract
 * * "IsExempt": <boolean>,
 * * "ToolId": <number:D2LID>|null,
 * * "ToolItemId":  <number:D2LID>|null,
 * * "ActivityType": <number:ACTIVITYTYPE_T>,
 * * "GradeItemId": <number:D2LID>|null,
 * * "LastModifiedDate": <string:UTCDateTime>|null,
 * * "AssociatedGradeItemIds": [<number:D2LID>, ...]  // Added with LMS v20.23.3
 * * }
 */
class ContentObjectTopic extends ContentObject {

	protected function getAvailableFields(): array {
		$base = parent::getAvailableFields();
		return array_merge($base, [
			'TopicType', 'Url', 'StartDate', 'EndDate', 'DueDate', 'IsBroken', 'OpenAsExternalResource',
			'IsExempt', 'ToolId', 'ToolItemId', 'ActivityType', 'GradeItemId', 'AssociatedGradeItemIds'
		]);
	}
}
