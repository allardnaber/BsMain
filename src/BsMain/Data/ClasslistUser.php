<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.ClasslistUser
 * {
    "Identifier": <string:D2LID>,
    "ProfileIdentifier": <string>,
    "DisplayName": <string>,
    "Username": <string>|null,
    "OrgDefinedId": <string>|null,
    "Email": <string>|null,
    "FirstName": <string>|null,
    "LastName": <string>|null,
    "RoleId": <number:D2LID>|null,   // NOTE: Only set if the user is allowed to search for this role
    "LastAccessed": <string:UTCDateTime>|null,
    "IsOnline": <boolean>,
    "ClasslistRoleDisplayName": <string>  // Added with LMS v20.22.12
}
 */
class ClasslistUser extends GenericObject {
	
	protected function getAvailableFields(): array {
		return [
			'Identifier', 'ProfileIdentifier', 'DisplayName', 'Username',
			'OrgDefinedId', 'Email', 'FirstName', 'LastName', 'RoleId',
			'LastAccessed', 'IsOnline', 'ClasslistRoleDisplayName'
			];
	}
}
