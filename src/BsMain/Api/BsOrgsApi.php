<?php

namespace BsMain\Api;

class BsOrgsApi extends BsResourceBaseApi {


	public function getOrgUnitAncestorByType(int $orgUnit, $orgUnitType) {
		$response = $this->request($this->url('/lp/1.31/orgstructure/%d/ancestors/?ouTypeId=%d', $orgUnit, $orgUnitType), 'the ancestor');
		return \BsMain\Data\OrgUnit::createArray($response, false);
	}
}