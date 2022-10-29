<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.RoleInfo
 * {
    "Id": <number:D2LID>,
    "Code": <string>|null,
    "Name": <string>
}
 */
class RoleInfo extends GenericObject {
	
	protected function getAvailableFields() {
		return [ 'Id', 'Code', 'Name' ];
	}

}