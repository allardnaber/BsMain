<?php

namespace BsMain\Api;


use BsMain\Data\UserOrgUnit;
use GuzzleHttp\Exception\GuzzleException;

class BsEnrollmentsApi extends BsResourceBaseApi {

	/**
	 * @param int $userId
	 * @param int $orgUnitType
	 * @param int $role
	 * @return UserOrgUnit[]
	 * @throws GuzzleException
	 */
	public function getUserEnrollmentsForTypeAndRole(int $userId, int $orgUnitType, int $role) {
		return $this->requestArray(
			$this->url('/lp/1.31/enrollments/users/%d/orgUnits/?orgUnitTypeId=%d&roleId=%d', $userId, $orgUnitType, $role),
			UserOrgUnit::class, true, 'user enrollments');
	}
}