<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/groups.html#Section.SectionSettingsData
 * {
	"Name": <string>,
	"Description": { <composite:RichText> },
	"EnrollmentStyle": <number:SECTENROLL_T>,
	"EnrollmentQuantity": <number>,
	"AutoEnroll": <boolean>,
	"RandomizeEnrollments": <boolean>,
	"DescriptionsVisibleToEnrollees": <boolean>  // Added as of LP API version 1.39
}
 */
class SectionSettingsDataFetch extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 
			'Name', 'EnrollmentStyle', 'EnrollmentQuantity', 'IsInitialized'
		];
	}

	protected function postCreationProcessing(): void {
		$this->IsInitialized = in_array($this->EnrollmentStyle,
			['PeoplePerSectionAutoEnrollment', 'NumberOfSectionsAutoEnrollment']);
	}
}
