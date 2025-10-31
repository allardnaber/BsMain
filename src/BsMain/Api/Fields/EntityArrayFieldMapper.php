<?php

namespace BsMain\Api\Fields;

class EntityArrayFieldMapper extends FieldMapper {

	public function map(mixed $input): mixed {
		return array_map(fn(array $i) => ($this->type)::newInstance($i), $input[$this->name]);
	}

}
