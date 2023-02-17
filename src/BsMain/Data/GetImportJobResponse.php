<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/course.html#Course.GetImportJobResponse
 * {
"JobToken": <string>,
"TargetOrgUnitId": <number:D2LID>,
"Status": <string:COI_IMPORTJOBTSTATUS_T>
}
 */
class GetImportJobResponse extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'JobToken', 'TargetOrgUnitId', 'Status' ];
	}
	
}
