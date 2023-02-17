<?php

namespace BsMain\Data;

/**
 * D2L requires:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Group.GroupData
{
    "Name": <string>,
    "Code": <string>,
    "Description": { <composite:RichTextInput> }
}
 */
class GroupDataCreate extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Name', 'Code', 'Description' ];
	}

	
}
