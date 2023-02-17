<?php

namespace BsMain\Api;

use BsMain\Data\OrgUnit;
use GuzzleHttp\Exception\GuzzleException;

class BsOrgsApi extends BsResourceBaseApi {

	/**
	 * @param int $orgUnit
	 * @param int $orgUnitType
	 * @return OrgUnit[]
	 * @throws GuzzleException
	 */
	public function getOrgUnitAncestorsByType(int $orgUnit, int $orgUnitType): array {
		return $this->requestArray($this->url('/lp/1.31/orgstructure/%d/ancestors/?ouTypeId=%d', $orgUnit, $orgUnitType),
			OrgUnit::class, false, 'the ancestor');
	}
}