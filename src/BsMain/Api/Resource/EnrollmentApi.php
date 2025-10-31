<?php

namespace BsMain\Api\Resource;

use BsMain\Data\Enrollment\ClasslistUser;

class EnrollmentApi extends ApiShell {

	/**
	 * @param int $courseId
	 * @param bool|null $onlyShowShownInGrades
	 * @param string|null $searchTerm
	 * @param int|null $roleId
	 * @return ClasslistUser[]
	 */
	public function getClasslist(int $courseId, ?bool $onlyShowShownInGrades = null, ?string $searchTerm = null, ?int $roleId = null): array {
		return ClasslistUser::get($this->client)
			->leUrl('%d/classlist/paged/', $courseId)
			->param('onlyShowShownInGrades', $this->boolToString($onlyShowShownInGrades))
			->param('searchTerm', $searchTerm)
			->param('roleId', $roleId)
			->paged()
			->fetchArray();
	}

	private function boolToString(?bool $value): ?string {
		if ($value === null) return null;
		return $value ? 'true' : 'false';
	}
}
