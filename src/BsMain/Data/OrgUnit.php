<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/orgunit.html#OrgUnit.OrgUnit
 * {
    "Identifier": <string:D2LID>,
    "Name": <string>,
    "Code": <string>|null,
    "Type": { <composite:OrgUnit.OrgUnitTypeInfo> }
}
 */
class OrgUnit extends GenericObject {
	
	protected function getAvailableFields(): array {
		return [ 'Identifier', 'Name', 'Code', 'Type' ];
	}

}