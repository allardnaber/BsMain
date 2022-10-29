<?php

namespace BsMain\Data;

/**
 * D2L requires:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Group.GroupCategoryData
{
    "Name": <string>,
    "Description": { <composite:RichTextInput> },
    "EnrollmentStyle": <number:GRPENROLL_T>,
    "EnrollmentQuantity": <number>|null,
    "AutoEnroll": <boolean>,
    "RandomizeEnrollments": <boolean>,
    "NumberOfGroups": <number>|null,
    "MaxUsersPerGroup": <number>|null,
    "AllocateAfterExpiry": <boolean>,
    "SelfEnrollmentExpiryDate": <string:UTCDateTime>|null,
    "GroupPrefix": <string>|null,
    "RestrictedByOrgUnitId": <number:D2LID>|null
}
 */
class GroupCategoryDataCreate extends GenericObject {

	protected function getAvailableFields() {
		return [
			'Name', 'Description', 'EnrollmentStyle', 'EnrollmentQuantity',
			'AutoEnroll', 'RandomizeEnrollments', 'NumberOfGroups',
			'MaxUsersPerGroup', 'AllocateAfterExpiry', 'SelfEnrollmentExpiryDate',
			'GroupPrefix', 'RestrictedByOrgUnitId'
		];
	}
	
}
