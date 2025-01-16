<?php

namespace BsMain\Api;

use BsMain\Data\BatchEnrollmentError;
use BsMain\Data\ClasslistUser;
use BsMain\Data\CreateEnrollmentData;
use BsMain\Data\EnrollmentData;
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

	public function createOrUpdateEnrollment(CreateEnrollmentData $data): EnrollmentData {
		return $this->request(
			$this->url('/lp/1.43/enrollments/'), EnrollmentData::class, 'enrollment',
			'POST', $data->getJson(true)
		);
	}

	/**
	 * @param CreateEnrollmentData[] $enrollments
	 * @return BatchEnrollmentError[]
	 */
	public function createAndUpdateEnrollmentsInBatch(array $enrollments): array {
		$errors = [];

		$chunks = array_chunk($enrollments, self::ENROLLMENT_BATCH_MAX_SIZE);
		foreach ($chunks as $chunk) {
			$errors += $this->requestArray(
				$this->url('/lp/1.45/enrollments/batch/'),
				BatchEnrollmentError::class,
				false,
				'enrollments',
				'POST',
				json_encode($chunk)
			);
		}
		return $errors;
	}
}
