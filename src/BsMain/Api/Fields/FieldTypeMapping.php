<?php

namespace BsMain\Api\Fields;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Api\Fields\Attributes\CustomMapper;
use BsMain\Data\ApiEntity;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;

class FieldTypeMapping {

	/**
	 * @var FieldMapper[]
	 */
	private array $fields = [];

	public function __construct(ReflectionClass $reflection) {
		$this->collect($reflection);
	}

	/**
	 * Recursively map fields based on their types.
	 * @param array $inputFields Raw input fields
	 * @param array $outputFields Field values mapped to the expected data types.
	 */
	public function mapFields(array $inputFields, array &$outputFields): void {
		foreach ($inputFields as $name => $value) {
			$outputFields[$name] = isset($this->fields[$name]) ? $this->fields[$name]->map($inputFields) : $value;
		}
	}

	/**
	 * @return string[]
	 */
	public function getFieldNames(): array {
		return array_keys($this->fields);
	}

	private function collect(ReflectionClass $reflection): void {
		foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			$type = $prop->getType();
			if ($type === null) {
				continue;
			}
			if ($type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
				throw new RuntimeException(sprintf(
					'Property %s of class %s has a union or intersection type, this is not supported. ' .
					'Omit type or use mixed, and cast it in post processing through #onCreate().',
					$reflection->getName(),
					$prop->getName())
				);
			}

			$customMapperAttribute = $prop->getAttributes(CustomMapper::class);
			$arrayOfAttribute = $prop->getAttributes(ArrayOf::class);

			if (!empty($customMapperAttribute)) {
				$attr = $customMapperAttribute[0]->newInstance();
				assert($attr instanceof CustomMapper);
				$this->fields[$prop->getName()] = new ($attr->getClassName())($prop->getName(), $type->getName());
			}

			// array of typed entities
			elseif (!empty($arrayOfAttribute) && $type->getName() === 'array') {
				$attr = $arrayOfAttribute[0]->newInstance();
				assert($attr instanceof ArrayOf);
				$this->fields[$prop->getName()] = new EntityArrayFieldMapper($prop->getName(), $attr->getClassName());
			}

			// built in types like string, int, etc.
			elseif ($type->isBuiltin()) {
				$this->fields[$prop->getName()] = new BuiltInFieldMapper($prop->getName(), $type->getName());
			}

			// date time field (supports DateTime, DateTimeImmutable or own subclasses of DateTimeInterface)
			elseif (is_a($type->getName(), DateTimeInterface::class, true)) {
				$classname = $type->getName() === DateTimeInterface::class ? DateTimeImmutable::class : $type->getName();
				$this->fields[$prop->getName()] = new DateTimeMapper($prop->getName(), $classname);
			}

			elseif (enum_exists($type->getName())) {
				$this->fields[$prop->getName()] = new EnumFieldMapper($prop->getName(), $type->getName());
			}

			// single typed entity
			elseif (is_subclass_of($type->getName(), ApiEntity::class)) {
				$this->fields[$prop->getName()] = new EntityFieldMapper($prop->getName(), $type->getName());
			}

			else {
				throw new RuntimeException(sprintf(
					'Unable to create field mapper for %s of type %s. Does the data type implement %s?',
					$prop->getName(), $type->getName(), ApiEntity::class));
			}

		}
	}



}
