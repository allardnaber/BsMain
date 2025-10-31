<?php

namespace BsMain\Api\Fields;

class BuiltInFieldMapper extends FieldMapper {

	public function map(mixed $input): mixed {
		return match ($this->type) {
			'int' => (int) $input[$this->name],
			'bool' => (bool) $input[$this->name],
			'float' => (float) $input[$this->name],
			//'mixed' => $input[$this->name],
			default => $input[$this->name],
		};
	}

}
