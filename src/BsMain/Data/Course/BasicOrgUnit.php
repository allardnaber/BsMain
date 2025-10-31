<?php

namespace BsMain\Data\Course;

use BsMain\Data\ApiEntity;

/**
 * {@see https://docs.valence.desire2learn.com/res/course.html#Course.BasicOrgUnit}
 */
class BasicOrgUnit extends ApiEntity{

	public int $Identifier;
	public string $Name;
	public string $Code;

}
