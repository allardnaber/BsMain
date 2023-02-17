<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/course.html#Course.CourseOffering
 * {
		"Identifier": <string:D2LID>,
		"Name": <string>,
		"Code": <string>,
		"IsActive": <boolean>,
		"Path": <string>,
		"StartDate": <string:UTCDateTime>|null,
		"EndDate": <string:UTCDateTime>|null,
		"CourseTemplate": { <composite:Course.BasicOrgUnit> }|null,
		"Semester": { <composite:Course.BasicOrgUnit> }|null,
		"Department": { <composite:Course.BasicOrgUnit> }|null,
		"Description": { <composite:RichText> },  // Added with LP API v1.26
		"CanSelfRegister": <boolean>  // Added with LP API v1.27
	}
 */
class CourseOffering extends GenericObject {
	
	protected function getAvailableFields(): array {
		return [ 
			'Identifier', 'Name', 'Code', 'IsActive', 'Path', 'StartDate', 'EndDate',
			'CourseTemplate', 'Semester', 'Department', 'Description', 'CanSelfRegister'
		];
	}

	public function getBrightspaceId(): int {
		return $this->Identifier;
	}

}
