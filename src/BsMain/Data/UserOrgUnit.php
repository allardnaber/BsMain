<?php

namespace BsMain\Data;

/**
 * D2L returns paged set of (excluding unstable)
 * see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.UserOrgUnit
 * {
    "OrgUnit": { <composite:Enrollment.OrgUnitInfo> },
    "Role": { <composite:Enrollment.RoleInfo> }
}
 */
class UserOrgUnit extends GenericObject {
	
	protected function getAvailableFields(): array {
		return [ 'OrgUnit', 'Role' ];
	}

	protected function postCreationProcessing(): void {
		$this->Role = new RoleInfo($this->Role);
		$this->OrgUnit = new OrgUnitInfo($this->OrgUnit);
	}

}