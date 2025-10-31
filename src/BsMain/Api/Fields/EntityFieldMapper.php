<?php

namespace BsMain\Api\Fields;

class EntityFieldMapper extends FieldMapper {

	public function map(array $input): mixed {
		return ($this->type)::newInstance($input[$this->name]);
	}

}
