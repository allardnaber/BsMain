<?php

namespace BsMain\Data;

/**
 * D2L sends:
 * see https://docs.valence.desire2learn.com/res/course.html#Course.CopyJobComplete
 * {
 * "JobToken": <string>,
 * "SourceOrgUnitId": <number:D2LID>,
 * "TargetOrgUnitId": <number:D2LID>,
 * "Status": <string:COPYJOBSTATUS_T>
 * }
 */
class CourseCopyJobComplete extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'JobToken', 'SourceOrgUnitId', 'TargetOrgUnitId', 'Status' ];
	}
	
}
