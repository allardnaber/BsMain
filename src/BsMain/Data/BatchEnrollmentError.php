<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.BatchEnrollmentError
 * {
 * "StatusCode": <number>,
 * "StatusMessage": <string>,
 * "OrgUnitId": <number:D2LID>,
 * "UserId": <number:D2LID>,
 * "RoleId": <number:D2LID>
 * }
 */
class BatchEnrollmentError extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'StatusCode', 'StatusMessage', 'OrgUnitId', 'UserId', 'RoleId' ];
	}
}
