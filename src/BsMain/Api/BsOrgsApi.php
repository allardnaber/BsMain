<?php

namespace BsMain\Api;

use BsMain\Data\OrgUnit;

class BsOrgsApi extends BsResourceBaseApi {

	/**
	 * @param array $queryParams Params from [orgUnitType, orgUnitCode, orgUnitName, exactOrgUnitCode, exactOrgUnitName]
	 * @return OrgUnit[] List of org units that match the provided parameters.
	 */
	public function getOrgUnits(array $queryParams = []): array {
		return $this->requestArray(
			$this->appendQueryParams(
				$this->url('/lp/1.35/orgstructure/'),
				$queryParams,
				['orgUnitType', 'orgUnitCode', 'orgUnitName', 'exactOrgUnitCode', 'exactOrgUnitName']
			),
			OrgUnit::class, true, 'list of schedule groups');
	}

	public function getOrgUnitAncestorsByType(int $orgUnit, int $orgUnitType): array {
		return $this->requestArray($this->url('/lp/1.31/orgstructure/%d/ancestors/?ouTypeId=%d', $orgUnit, $orgUnitType),
			OrgUnit::class, false, 'the ancestor');
	}
}