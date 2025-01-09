<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/groups.html#Group.GroupCategoryData
 * {
 * "GroupCategoryId": <number:D2LID>,
 * "Name": <string>,
 * "Description": { <composite:RichText> },
 * "EnrollmentStyle": <number:GRPENROLL_T>,
 * "EnrollmentQuantity": <number>|null,
 * "MaxUsersPerGroup": <number>|null,
 * "AutoEnroll": <boolean>,
 * "RandomizeEnrollments": <boolean>,
 * "Groups": [ <number:D2LID>, ... ],
 * "AllocateAfterExpiry": <boolean>,
 * "SelfEnrollmentExpiryDate": <string:UTCDateTime>|null,
 * "RestrictedByOrgUnitId": <number:D2LID>|null,
 * "DescriptionsVisibleToEnrolees": <boolean>
 * }
 */
class GroupCategoryDataFetch extends GenericObject {

	protected function getAvailableFields(): array {
		return [
			'GroupCategoryId', 'Name', 'Description', 'EnrollmentStyle', 'EnrollmentQuantity', 'MaxUsersPerGroup',
			'AutoEnroll', 'RandomizeEnrollments', 'Groups', 'AllocateAfterExpiry', 'SelfEnrollmentExpiryDate',
			'RestrictedByOrgUnitId', 'DescriptionsVisibleToEnrolees'
		];
	}

	public function getBrightspaceId(): int {
		return $this->GroupCategoryId;
	}
}
