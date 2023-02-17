<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/user.html#User.CreateUserData
 * {
    "Identifier": <string:D2LID>,
    "ProfileIdentifier": <string>,
    "DisplayName": <string>,
    "UserName": <string>|null,  -->NOTE: The API uses Username
    "OrgDefinedId": <string>|null,
    "Email": <string>|null,
    "FirstName": <string>|null,
    "LastName": <string>|null,
    "RoleId": <number:D2LID>|null,
    "LastAccessed": <string:UTCDateTime>|null,
    "IsOnline": <boolean>
}
 */
class ClasslistUser extends GenericObject {
	
	protected function getAvailableFields(): array {
		return [
			'Identifier', 'ProfileIdentifier', 'DisplayName', 'Username',
			'OrgDefinedId', 'Email', 'FirstName', 'LastName', 'RoleId',
			'LastAccessed', 'IsOnline'
			];
	}
}
