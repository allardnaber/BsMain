<?php

namespace BsMain\Data;

use BsMain\Api\Fields\FieldTypeMapping;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

abstract class ApiEntity implements JsonSerializable {

	/**
	 * All data fields, that may have varying types.
	 * @var array The values, indexed by column name.
	 */
	private array $__int_fields = [];

	/**
	 * Keep track of updated fields.
	 * @var bool[] indexed by property name
	 */
	private array $__int_dirty = [];

	/**
	 * Cached empty instances, to efficiently create new objects.
	 * @var self[]
	 */
	private static array $newInstanceTemplates = [];

	/**
	 * @var FieldTypeMapping[]
	 */
	private static array $fieldTypeMappings = [];

	public function __construct(?array $props = null) {
		$reflection = new ReflectionClass(static::class);
		if (!isset(ApiEntity::$fieldTypeMappings[static::class])) {
			ApiEntity::$fieldTypeMappings[static::class] = new FieldTypeMapping($reflection);
		}
		foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			unset($this->{$prop->getName()});
		}

		self::initFields($this, $props);
	}

	public static function newInstance(?array $props = null): static {
		if (!isset(ApiEntity::$newInstanceTemplates[static::class])) {
			ApiEntity::$newInstanceTemplates[static::class] = new static();
		}
		$instance = clone ApiEntity::$newInstanceTemplates[static::class];
		self::initFields($instance, $props);
		return $instance;
	}

	private static function initFields(self $instance, ?array $props): void {
		if ($props !== null) {
			ApiEntity::$fieldTypeMappings[static::class]->mapFields($props, $instance->__int_fields);
			$instance->onCreate();
		}
	}

	/**
	 * Can be used for postprocessing after creation of object
	 * @return void
	 */
	protected function onCreate(): void {}

	public function __set(string $name, mixed $value): void {
		if (!isset($this->__int_fields[$name]) || $this->__int_fields[$name] !== $value) {
			$this->__int_fields[$name] = $value;
			$this->__int_dirty[$name] = true;
		}
	}

	public function __get(string $name): mixed {
		return $this->__int_fields[$name] ?? null;
	}

	public function isChanged(): bool {
		return !empty($this->__int_dirty);
	}

	/**
	 * @return string[]
	 */
	protected function getDirtyFields(): array {
		return array_keys($this->__int_dirty);
	}

	protected function resetDirtyState(): void {
		$this->__int_dirty = [];
	}

	public function jsonSerialize(): array {
		return $this->__int_fields;
	}

}
