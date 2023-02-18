<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/user.html#User.WhoAmIUser
 * {
"Identifier": <string:D2LID>,
"FirstName": <string>,
"LastName": <string>,
"UniqueName": <string>,
"ProfileIdentifier": <string>,
"Pronouns": <string>
}
 */
class WhoAmIUser extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Identifier', 'FirstName', 'LastName', 'UniqueName', 'ProfileIdentifier', 'Pronouns' ];
	}

}
