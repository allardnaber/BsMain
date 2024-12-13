<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/user.html#User.CreateUserData
 * {
    "OrgDefinedId": <string>|null,
    "FirstName": <string>,
    "MiddleName": <string>|null,
    "LastName": <string>,
    "ExternalEmail": <string>|null,
    "UserName": <string>,
    "RoleId": <number>,
    "IsActive": <boolean>,
    "SendCreationEmail": <boolean>,
    "Pronouns": <string>|null
}
*/
class CreateUserData extends GenericObject {
	
	protected function getAvailableFields(): array {
		return [ 
			'OrgDefinedId', 'FirstName', 'MiddleName', 'LastName', 'ExternalEmail', 'UserName',
			'RoleId', 'IsActive', 'SendCreationEmail', 'Pronouns'
		];
	}

}
