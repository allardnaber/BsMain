<?php

namespace BsMain\Data;

/**
 * D2L requires:
 * see https://docs.valence.desire2learn.com/res/groups.html#Group.GroupCategoryData
 * {
 *
 * "Name": <string>,
 * "Description": { <composite:RichTextInput> },
 * "EnrollmentStyle": <number:GRPENROLL_T>,
 * "EnrollmentQuantity": <number>|null,
 * "AutoEnroll": <boolean>,
 * "RandomizeEnrollments": <boolean>,
 * "NumberOfGroups": <number>|null,
 * "MaxUsersPerGroup": <number>|null,
 * "AllocateAfterExpiry": <boolean>,
 * "SelfEnrollmentExpiryDate": <string:UTCDateTime>|null,
 * "GroupPrefix": <string>|null,
 * "RestrictedByOrgUnitId": <number:D2LID>|null,
 * "DescriptionsVisibleToEnrolees": <boolean>
 * }
 */
class GroupCategoryDataCreate extends GenericObject {

	protected function getAvailableFields(): array {
		return [
			'Name', 'Description', 'EnrollmentStyle', 'EnrollmentQuantity', 'MaxUsersPerGroup',
			'AutoEnroll', 'RandomizeEnrollments', 'Groups', 'AllocateAfterExpiry', 'SelfEnrollmentExpiryDate',
			'RestrictedByOrgUnitId', 'DescriptionsVisibleToEnrolees'
		];
	}
	
}
