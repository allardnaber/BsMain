<?php

namespace BsMain\Api\Fields;

class EnumFieldMapper extends FieldMapper {

	public function map(array $input): mixed {
		return ($this->type)::from($input[$this->name]);
	}

}
