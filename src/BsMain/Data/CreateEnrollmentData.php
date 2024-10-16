<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.CreateEnrollmentData
 * {
 * "OrgUnitId": <number:D2LID>,
 * "UserId": <number:D2LID>,
 * "RoleId": <number:D2LID>
 * }
 */

class CreateEnrollmentData extends GenericObject implements \JsonSerializable {

	protected function getAvailableFields(): array {
		return [ 'OrgUnitId', 'UserId', 'RoleId' ];
	}

	public function jsonSerialize(): array {
		return $this->getAllFields();
	}
}
