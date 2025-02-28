<?php

namespace BsMain\Api;

use BsMain\Data\CourseCreateCopyJobRequest;
use BsMain\Data\CourseOffering;
use BsMain\Data\CreateCopyJobResponse;
use BsMain\Data\FileSystemObject;
use BsMain\Data\GetImportJobResponse;
use BsMain\Data\JobToken;

class BsCoursesApi extends BsResourceBaseApi {

	public function getCourseOffering(int $courseId): CourseOffering {
		return $this->request($this->url('/lp/1.31/courses/%d', $courseId), CourseOffering::class, 'the course');
	}

	public function importCourseContent(int $courseId, string $filename): JobToken {
		$options = $this->addFileToMultipartOptions('file', $filename, 'application/zip');
		return $this->request(
				$this->url('/le/1.51/import/%d/imports/', $courseId),
				JobToken::class, 'the import job', 'POST', null, $options);
	}

	public function getImportCourseContentJobStatus(int $courseId, string $jobToken): GetImportJobResponse {
		return $this->request(
			$this->url('/le/1.51/import/%d/imports/%s', $courseId, $jobToken),
			GetImportJobResponse::class, 'the import status');
	}

	public function copyCourse(int $targetCourseId, CourseCreateCopyJobRequest $request): CreateCopyJobResponse {
		return $this->request(
			$this->url('/le/1.75/import/%d/copy/', $targetCourseId),
			CreateCopyJobResponse::class, 'the course copy job', 'POST',
			$request->getJson(true)
		);
	}

	/**
	 * @param int $courseId
	 * @return FileSystemObject[]
	 */
	public function getCourseFileListing(int $courseId): array {
		return $this->requestArray(
			$this->url('/lp/1.46/%d/managefiles/', $courseId), FileSystemObject::class, true
		);
	}

}
