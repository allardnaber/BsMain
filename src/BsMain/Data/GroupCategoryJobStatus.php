<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Group.GroupCategoryJobStatus
 * {
    "Status": <int:GROUPSJOBSTATUS_T>
}
 */
class GroupCategoryJobStatus extends GenericObject {

	protected function getAvailableFields() {
		return [ 
			'Status'
		];
	}
	
}
