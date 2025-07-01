<?php

namespace BsMain\Api;

use BsMain\Data\GradeObject;

class BsGradesApi extends BsResourceBaseApi {

	/**
	 * @param int $courseId
	 * @return GradeObject[]
	 */
	public function getGradeItems(int $courseId): array {
		return $this->requestArray($this->url('/le/1.75/%d/grades/', $courseId),
			GradeObject::class, false, 'list of grade items'
		);
	}

	public function deleteGradeItem(int $courseId, int $gradeItemId): void {
		$this->request(
			$this->url('/le/1.75/%d/grades/%d', $courseId, $gradeItemId), null, 'the grade item', 'DELETE'
		);
	}

}
