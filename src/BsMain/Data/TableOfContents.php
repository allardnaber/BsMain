<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/content.html#ToC.TableOfContents
 * {
   "Modules": [ // Array of Module blocks
        {
            "ModuleId": <number:D2LID>,
            "Title": <string>,
            "Modules": [ // Array of child Module blocks
                {
                    "ModuleId": <number:D2LID>,
                    "Title": <string>,
                    "SortOrder": <number>,
                    "StartDateTime": <string:UTCDateTime>|null,
                    "EndDateTime": <string:UTCDateTime>|null,
                    "Modules": [ { <composite:ToC.Module> }, ... ],
                    "Topics": [ { <composite:ToC.Topic> }, ... ],
                    "IsHidden": <boolean>,
                    "IsLocked": <boolean>,
                    "PacingStartDate": <string:ISODate>|null,
                    "PacingEndDate": <string:ISODate>|null,
                    "DefaultPath": <string>,
                    "LastModifiedDate": <string:UTCDateTime>|null
                },
                { <composite:Module> }, ... ],
            "Topics": [ // Array of Topic blocks
                {
                    "TopicId": <number:D2LID>,
                    "Identifier": <string:D2LID>,
                    "TypeIdentifier": <string>,
                    "Title": <string>,
                    "Bookmarked": <boolean>,
                    "Unread": <boolean>,
                    "Url": <string>,
                    "SortOrder": <number>,
                    "StartDateTime": <string:UTCDateTime>|null,
                    "EndDateTime": <string:UTCDateTime>|null,
                    "ActivityId": <string>|null,
                    "CompletionType": <number:CONTENT_COMPLETIONTYPE_T>,
                    "IsExempt": <boolean>,
                    "IsHidden": <boolean>,
                    "IsLocked": <boolean>,
                    "IsBroken": <boolean>,
                    "ToolId": <number:D2LID>|null,
                    "ToolItemId":  <number:D2LID>|null,
                    "ActivityType": <number:ACTIVITYTYPE_T>,
                    "GradeItemId": <number:D2LID>|null,
                    "LastModifiedDate": <string:UTCDateTime>|null,
                    "StartDateAvailabilityType": <AVAILABILITY_T>|null,  // Available in LE's unstable contract
                    "EndDateAvailabilityType": <AVAILABILITY_T>|null  // Available in LE's unstable contract
                },
                { <composite:Topic> }, ... ]
        },
        { <composite:Module> }, ... ]
}
 */
class TableOfContents extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Modules' ];
	}

	protected function postCreationProcessing(): void {
		$this->Modules = Module::array($this->Modules);
	}
}
