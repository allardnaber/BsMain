<?php

namespace BsMain\Api\Fields\Attributes;

use Attribute;
use BsMain\Data\ApiEntity;
use RuntimeException;

/**
 * Indicates class type for an entity array.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
readonly class ArrayOf {
	public function __construct(private string $className) {
		if (!is_subclass_of($this->className, ApiEntity::class)) {
			throw new RuntimeException(sprintf('Class %s must be a subclass of %s.', $this->className, ApiEntity::class));
		}
	}

	public function getClassName(): string { return $this->className; }
}
