<?php

namespace BsMain\Data\Course;

use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

/**
 *
 * {@see https://docs.valence.desire2learn.com/res/course.html#Course.CourseOffering}
 */
class CourseOffering extends ApiEntity {

	public int $Identifier;
	public string $Name;
	public string $Code;
	public bool $IsActive;
	public string $Path;
	public ?string $StartDate;
	public ?string $EndDate;
	public ?BasicOrgUnit $CourseTemplate;
	public ?BasicOrgUnit $Semester;
	public ?BasicOrgUnit $Department;
	public RichText $Description;
	public bool $CanSelfRegister;
}
