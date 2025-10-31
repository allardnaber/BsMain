<?php

namespace BsMain\Data\Course;

use BsMain\Data\ApiEntity;

/**
 * see https://docs.valence.desire2learn.com/res/course.html#Course.FileSystemObject
 */
class FileSystemObject extends ApiEntity {

	public string $Name;
	public FileSystemObjectType_T $FileSystemObjectType;

}

