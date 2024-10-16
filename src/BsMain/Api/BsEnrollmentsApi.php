<?php

namespace BsMain\Api;

use BsMain\Data\BatchEnrollmentError;
use BsMain\Data\ClasslistUser;
use BsMain\Data\CreateEnrollmentData;
use BsMain\Data\MyOrgUnitInfo;
use BsMain\Data\UserOrgUnit;

class BsEnrollmentsApi extends BsResourceBaseApi {

	private const ENROLLMENT_BATCH_MAX_SIZE = 1000;

	/**
	 * @param int $userId
	 * @param int $orgUnitType
	 * @param int $role
	 * @return UserOrgUnit[]
	 */
	public function getUserEnrollmentsForTypeAndRole(int $userId, int $orgUnitType, int $role): array {
		return $this->requestArray(
			$this->url('/lp/1.31/enrollments/users/%d/orgUnits/?orgUnitTypeId=%d&roleId=%d', $userId, $orgUnitType, $role),
			UserOrgUnit::class, true, 'user enrollments');
	}

	/**
	 * @param int $orgUnit
	 * @return ClasslistUser[]
	 */
	public function getClasslistForOrgUnit(int $orgUnit): array {
		return $this->requestArray($this->url('/le/1.67/%d/classlist/paged/', $orgUnit),
			ClasslistUser::class, true, 'enrolled users');
	}

	public function getMyEnrollmentForOrgUnit(int $orgUnit): MyOrgUnitInfo {
		return $this->request($this->url('/lp/1.43/enrollments/myenrollments/%d', $orgUnit), MyOrgUnitInfo::class, 'enrollment details');
	}

	/**
	 * @param CreateEnrollmentData[] $enrollments
	 * @return BatchEnrollmentError[]
	 */
	public function createEnrollmentsInBatch(array $enrollments): array {
		$runs = ceil(count($enrollments) / self::ENROLLMENT_BATCH_MAX_SIZE);
		$errors = [];
		for ($run = 0; $run < $runs; $run++) {
			$slice = array_slice($enrollments, $run * self::ENROLLMENT_BATCH_MAX_SIZE, self::ENROLLMENT_BATCH_MAX_SIZE);

			$errors += $this->requestArray(
				$this->url('/lp/1.45/enrollments/batch/'),
				BatchEnrollmentError::class,
				false,
				'enrollments',
				'POST',
				json_encode($slice)
			);
		}
		return $errors;
	}
}
