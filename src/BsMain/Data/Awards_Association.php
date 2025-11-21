<?php

namespace BsMain\Data;

class Awards_Association extends GenericObject {

	protected function getAvailableFields(): array {
		return ['AssociationId', 'OrgUnitId', 'Award'];
	}
}