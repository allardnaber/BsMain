<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/user.html#User.CreateUserData
 * {
		"OrgId": <number:D2LID>,
		"UserId": <number:D2LID>,
		"FirstName": <string>,
		"MiddleName": <string>|null,
		"LastName": <string>,
		"UserName": <string>,
		"ExternalEmail": <string>|null,
		"OrgDefinedId": <string>|null,
		"UniqueIdentifier": <string>,
		"Activation": { <composite:User.UserActivationData> },
		"LastAccessedDate": <string:UTCDateTime>|null,
		"Pronouns": <string>  // Added with LP API v1.33
	}
 */
class UserData extends GenericObject {
	
	protected function getAvailableFields() {
		return [ 
			'OrgId', 'UserId', 'FirstName', 'MiddleName', 'LastName', 'UserName', 'ExternalEmail', 
			'OrgDefinedId', 'UniqueIdentifier', 'Activation', 'LastAccessedDate', 'Pronouns'
		];
	}

}