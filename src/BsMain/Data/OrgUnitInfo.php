<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.OrgUnitInfo
 * {
    "Id": <number:D2LID>,
    "Type": { <composite:OrgUnit.OrgUnitTypeInfo> },
    "Name": <string>,
    "Code": <string>|null,
    "HomeUrl": <string:URL>|null,
    "ImageUrl": <string:APIURL>|null
}
 */
class OrgUnitInfo extends GenericObject {
	
	protected function getAvailableFields() {
		return [ 'Id', 'Type', 'Name', 'Code' ];
	}

}