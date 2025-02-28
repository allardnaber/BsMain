<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/course.html#Course.CreateCopyJobResponse
 * {
 * "JobToken": <string>
 * }
 */
class CreateCopyJobResponse extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'JobToken' ];
	}
	
}
