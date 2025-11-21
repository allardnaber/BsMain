<?php

namespace BsMain\Data;

class Awards_AssociationCreate extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'AwardId', 'Credit', 'HiddenAward' ];
	}
}
