<?php

namespace BsMain\Data;


use BsMain\Exception\BsAppApiException;
use BsMain\Exception\BsAppRuntimeException;

/**
 * see https://docs.valence.desire2learn.com/res/course.html#Course.FileSystemObject
 *
 * {
 * "Name": <string>,
 * "FileSystemObjectType": <FILESYSTEMOBJECTTYPE_T>
 * }
 */
class FileSystemObject extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Name', 'FileSystemObjectType' ];
	}
}
