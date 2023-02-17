<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Group.GroupsJobData
 * {
    "OrgUnitId": <number:D2LID>,
    "CategoryId": <number:D2LID>,
    "SubmitDate": <string:UTCDateTime>,
    "Status": <int:GROUPSJOBSTATUS_T>
}
 */
class GroupsJobData extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 
			'OrgUnitId', 'CategoryId', 'Status'
		];
	}
	
}
