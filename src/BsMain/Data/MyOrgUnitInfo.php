<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.MyOrgUnitInfo
 *
 * "OrgUnit": { <composite:Enrollment.OrgUnitInfo> },
 * "Access": {
 * "IsActive": <boolean>,
 * "StartDate": <string:UTCDateTime>|null,
 * "EndDate": <string:UTCDateTime>|null,
 * "CanAccess": <boolean>,
 * "ClasslistRoleName": <string>|null,
 * "LISRoles": [ <string>, ... ],
 * "LastAccessed": <string:UTCDateTime>|null
 * },
 * "PinDate": <string:UTCDateTime>|null
 * }
 */
class MyOrgUnitInfo extends GenericObject {

	protected function getAvailableFields(): array {
		return [
			'OrgUnit', 'Access', 'PinDate'
		];
	}

	protected function postCreationProcessing(): void {
		$this->OrgUnit = new OrgUnitInfo($this->OrgUnit);
	}
}
