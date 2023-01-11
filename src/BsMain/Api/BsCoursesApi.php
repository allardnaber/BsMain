<?php

namespace BsMain\Api;

use BsMain\Data\CourseOffering;

class BsCoursesApi extends BsResourceBaseApi {

	public function getCourseOffering(int $courseId): CourseOffering {
		$response = $this->request($this->url('/lp/1.31/courses/%d', $courseId), 'the course');
		return new CourseOffering($response);
	}
}