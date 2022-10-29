<?php

namespace BsMain\Data;

/**
 * D2L expects:
 * see https://docs.valence.desire2learn.com/res/groups.html#Section.SectionSettingsData
 * {
"EnrollmentStyle": <number:SECTENROLL_T>,
"EnrollmentQuantity": <number>,
"AutoEnroll": <boolean>,
"RandomizeEnrollments": <boolean>,
"DescriptionsVisibleToEnrollees": <boolean>  // Added as of LP API version 1.39
}
 */
class SectionSettingsDataCreate extends GenericObject {

	protected function getAvailableFields() {
		return [
			'EnrollmentStyle', 'EnrollmentQuantity', 'AutoEnroll', 'RandomizeEnrollments', 'DescriptionsVisibleToEnrollees'
		];
	}

}
