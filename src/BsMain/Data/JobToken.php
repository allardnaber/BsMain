<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/course.html#post--d2l-api-le-(version)-import-(orgUnitId)-imports-
 * {
"JobToken": <string>
}
 */
class JobToken extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'JobToken' ];
	}
	
}
