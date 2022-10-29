<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Group.GroupData
{
    "GroupId": <number:D2LID>,
    "Name": <string>,
    "Code": <string>,  // Added as of LMS v20.21.8
    "Description": { <composite:RichText> },
    "Enrollments": [ <number:D2LID>, ... ]
}
 */
class SectionDataFetch extends GroupDataFetch {

	protected function getAvailableFields() {
		return [ 'GroupId', 'SectionId', 'Name', 'Code', 'Description', 'Enrollments' ];
	}

	protected function postCreationProcessing() {
		$this->GroupId = $this->SectionId;
	}

	
}
