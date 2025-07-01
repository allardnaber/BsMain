<?php

namespace BsMain\Data;

/**
 * D2L expects:
 * see https://docs.valence.desire2learn.com/res/course.html#Course.CreateCopyJobRequest
 * {
 * "SourceOrgUnitId": <number:D2LID>,
 * "Components": [ <string:COURSECOMPONENT_T>, ... ]|null,
 * "CallbackUrl": <string>|null,
 * "DaysToOffsetDates": <number:integer>|null,  // Optional, see notes
 * "HoursToOffsetDates": <number:decimal>|null,  // Optional, see notes
 * "OffsetByStartDateDifference": <boolean>|null  // Optional, see notes
 * }
 */
class CourseCreateCopyJobRequest extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'SourceOrgUnitId', 'Components', 'CallbackUrl' ];
	}
	
}
