<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/user.html#User.Role
 * {
    "Identifier": <string>,
    "DisplayName": <string>,
    "Code": <string>,
    "Description": <string>,  // Available in LP's unstable contract
    "RoleAlias": <string>,  // Available in LP's unstable contract
    "IsCascading": <boolean>,  // Available in LP's unstable contract
    "AccessFutureCourses": <boolean>,  // Available in LP's unstable contract
    "AccessInactiveCourses": <boolean>,  // Available in LP's unstable contract
    "AccessPastCourses": <boolean>,  // Available in LP's unstable contract
    "ShowInGrades": <boolean>,  // Available in LP's unstable contract
    "ShowInUserProgress": <boolean>,  // Available in LP's unstable contract
    "InClassList": <boolean>,  // Available in LP's unstable contract
}
 */
class Role extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 
			'Identifier', 'DisplayName', 'Code', 'Description', 'RoleAlias',
			'IsCascading', 'AccessFutureCourses', 'AccessInactiveCourses',
			'AccessPastCourses', 'ShowInGrades', 'ShowInUserProgress',
			'InClassList'
		];
	}
	
}
