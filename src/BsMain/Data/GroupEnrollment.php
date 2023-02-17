<?php

namespace BsMain\Data;

/**
 * D2L requires:
 * see https://docs.valence.desire2learn.com/res/enroll.html#Group.GroupEnrollment
 * {
    "UserId": <number:D2LID>
}
 */
class GroupEnrollment extends GenericObject {
	
	protected function getAvailableFields(): array {
		return [ 'UserId' ];
	}

}
