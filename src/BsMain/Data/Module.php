<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/content.html#ToC.TableOfContents
 * // Array of child Module blocks
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
 */
class Module extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'ModuleId', 'Title', 'SortOrder', 'Modules', 'Topics', 'IsHidden', 'DefaultPath' ];
	}

	protected function postCreationProcessing(): void {
		$this->Modules = Module::array($this->Modules);
		$this->Topics = Topic::array($this->Topics);
	}
}
