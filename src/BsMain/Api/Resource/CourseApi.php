<?php

namespace BsMain\Api\Resource;

use BsMain\Data\Course\CourseOffering;
use BsMain\Data\Course\FileSystemObject;
use BsMain\Data\Course\GetImportJobResponse;

/**
 * API endpoints related to Course Offerings, {@see https://docs.valence.desire2learn.com/res/course.html}.<br>
 * Completeness: 3 / 29
 */
class CourseApi extends ApiShell {

	public function getCourseOffering(int $courseId): CourseOffering {
		return CourseOffering::get($this->client)
			->description('course offering')
			->lpUrl('courses/%d', $courseId)
			->fetch();
	}

	/*public function importCourseContent(int $courseId, string $filename): JobToken {
		$options = $this->addFileToMultipartOptions('file', $filename, 'application/zip');
		return $this->request(
			$this->url('/le/1.51/import/%d/imports/', $courseId),
			JobToken::class, 'the import job', 'POST', null, $options);
	}*/

	public function getImportCourseContentJobStatus(int $courseId, string $jobToken): GetImportJobResponse {
		return GetImportJobResponse::get($this->client)
			->description('course content import status')
			->leUrl('import/%d/imports/%s', $courseId, $jobToken)
			->fetch();
	}

	/*public function copyCourse(int $targetCourseId, CourseCreateCopyJobRequest $request): CreateCopyJobResponse {
		return $this->request(
			$this->url('/le/1.75/import/%d/copy/', $targetCourseId),
			CreateCopyJobResponse::class, 'the course copy job', 'POST',
			$request->getJson(true)
		);
	}*/

	/**
	 * Retrieve the direct child contents (folders and files) of an org unit path.<br>
	 * This action returns folders first followed by files both sorted alphabetically by name.
	 * @param int $courseId
	 * @param string|null $path Optional. Path relative to the course path, if not specified the course path is used.
	 * @return FileSystemObject[]
	 */
	public function getCourseFileListing(int $courseId, ?string $path = null): array {
		return FileSystemObject::get($this->client)
			->description('course file listing')
			->lpUrl('%d/managefiles/' ,$courseId)
			->param('path', $path)
			->fetchArray();
	}
}
