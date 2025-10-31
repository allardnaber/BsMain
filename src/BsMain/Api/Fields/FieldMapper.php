<?php

namespace BsMain\Api\Fields;

abstract class FieldMapper {

	public function __construct(protected string $name, protected string $type) {}

	/**
	 * Maps the entity input values to the desired output value. Use `$this->name` to get the related property,
	 * `$this->type` to get the expected class type.
	 * @param array $input The associative array with all fields of the input object included.
	 * @return mixed
	 */
	public abstract function map(array $input): mixed;

}
