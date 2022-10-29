<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Group.GroupEnrollment
 * {
    "LocaleId": <number:D2LID>,
    "LocaleName": <string>,
    "IsDefault": <boolean>,
    "CultureCode": <string>,
    "LanguageId": <number:D2LID>  // Added as of LMS v20.20.7
}
 */
class Locale extends GenericObject {
	
	protected function getAvailableFields() {
		return [ 'LocaleId', 'LocaleName', 'IsDefault', 'CultureCode', 'LanguageId' ];
	}

}
