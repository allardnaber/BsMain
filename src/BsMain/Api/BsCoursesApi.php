<?php

namespace BsMain\Api;

use BsMain\Data\CourseOffering;
use BsMain\Data\GetImportJobResponse;
use BsMain\Data\JobToken;

class BsCoursesApi extends BsResourceBaseApi {

	public function getCourseOffering(int $courseId): CourseOffering {
		$response = $this->request($this->url('/lp/1.31/courses/%d', $courseId), 'the course');
		return new CourseOffering($response);
	}

	public function importCourseContent(int $courseId, string $filename): JobToken {
		$options = $this->addFileToMultipartOptions('file', $filename, 'application/zip');
		return new JobToken(
			$this->request(
				$this->url('/le/1.51/import/%d/imports/', $courseId),
				'the import job', 'POST', null, $options)
		);
	}

	public function getImportCourseContentJobStatus(int $courseId, string $jobToken): GetImportJobResponse {
		return new GetImportJobResponse($this->request(
			$this->url('/le/1.51/import/%d/imports/%s', $courseId, $jobToken), 'the import status')
		);

	}
}