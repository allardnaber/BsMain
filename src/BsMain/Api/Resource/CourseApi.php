<?php

namespace BsMain\Api\Resource;

use BsMain\Data\CourseOffering;

class CourseApi extends ApiShell {

	public function getCourseOffering(int $courseId): CourseOffering {
		return CourseOffering::get($this->client)
			->lpUrl('courses/%d', $courseId)
			->fetch();
	}

}
