<?php

namespace BsMain\Api;

use BsMain\Data\Awards_Association;
use BsMain\Data\Awards_AssociationCreate;

class BsAwardsApi extends BsResourceBaseApi {

	public function addAwardToCourse(int $courseId, Awards_AssociationCreate $association): Awards_Association {
		return $this->request(
			$this->url('/bas/1.4/orgUnits/%d/associations/', $courseId), Awards_Association::class, 'award association',
			'POST', $association->getJson(true)
		);
	}

}
