<?php

namespace BsMain\Data;

/**
 * Adds the following fields to ContentObjectData
 * See https://docs.valence.desire2learn.com/res/content.html#Content.ContentObjectData
Topic.
{
"Title": <string>,
"ShortTitle": <string>,
"Type": 1,
"TopicType": <number:TOPIC_T>,
"Url": <string>,
"StartDate": <string:UTCDateTime>|null,
"EndDate": <string:UTCDateTime>|null,
"DueDate": <string:UTCDateTime>|null,
"IsHidden": <boolean>,
"IsLocked": <boolean>,
"OpenAsExternalResource": <boolean>|null,
"Description": { <composite:RichTextInput> }|null,
"MajorUpdate": <boolean>|null,
"MajorUpdateText": <string>,
"ResetCompletionTracking": <boolean>|null,
"Duration": <number>|null  // Available in LE's unstable contract
}*/
class ContentObjectDataTopic extends ContentObjectData {

	protected function getAvailableFields(): array {
		$base = parent::getAvailableFields();
		return array_merge($base, [
			'TopicType', 'Url', 'StartDate', 'EndDate', 'DueDate', 'OpenAsExternalResource'
		]);
	}
}
