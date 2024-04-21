<?php

namespace BsMain\Api;

use BsMain\Data\ContentObject;
use BsMain\Data\ContentObjectData;
use BsMain\Data\CourseOffering;
use BsMain\Data\GetImportJobResponse;
use BsMain\Data\JobToken;
use BsMain\Data\TableOfContents;

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

	public function getCourseToc(int $courseId, ?int $moduleId = null): TableOfContents {
		return $this->request(
			$moduleId === null
				? $this->url('/le/1.67/%d/content/toc', $courseId)
				: $this->url('/le/1.67/%d/content/toc?moduleId=%d', $courseId, $moduleId),
			TableOfContents::class, 'the table of contents');
	}

	public function addContentObject(int $courseId, int $parentModuleId, ContentObjectData $contentObject): ContentObject {
		return $this->request(
			$this->url('/le/1.67/%d/content/modules/%d/structure/', $courseId, $parentModuleId),
			ContentObject::class, 'the content object', 'POST',
		$contentObject->getJson(true));
	}
}