<?php

namespace BsMain\Api;


use BsMain\Data\ClasslistUser;
use BsMain\Data\UserOrgUnit;
use GuzzleHttp\Exception\GuzzleException;

class BsEnrollmentsApi extends BsResourceBaseApi {

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
	 * RENAMED FROM getUsersByOrgUnit
	 * @param int $orgUnit
	 * @return ClasslistUser[]
	 */
	public function getClasslistForOrgUnit(int $orgUnit): array {
		return$this->requestArray($this->url('/le/1.51/%d/classlist/paged/', $orgUnit),
			ClasslistUser::class, true, 'enrolled users');
	}
}