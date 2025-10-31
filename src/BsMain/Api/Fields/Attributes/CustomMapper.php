<?php

namespace BsMain\Api\Fields\Attributes;

use Attribute;
use BsMain\Api\Fields\FieldMapper;
use RuntimeException;

#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class CustomMapper {
	public function __construct(private string $className) {
		if (!is_subclass_of($this->className, FieldMapper::class)) {
			throw new RuntimeException(sprintf(
				'Custom mapper class %s must be a subclass of %s.',
				$this->className,
				FieldMapper::class
			));
		}
	}

	public function getClassName(): string { return $this->className; }
}
