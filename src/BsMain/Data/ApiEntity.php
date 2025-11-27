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
		$subClass = static::getSubClass($props);
		if ($subClass === null || $subClass === static::class) {
			if (!isset(ApiEntity::$newInstanceTemplates[static::class])) {
				ApiEntity::$newInstanceTemplates[static::class] = new static();
			}
			$instance = clone ApiEntity::$newInstanceTemplates[static::class];
			self::initFields($instance, $props);
			return $instance;
		} else {
			return $subClass::newInstance($props);
		}
	}

	/**
	 * Allows an entity to specify final subclass based on the object properties. The entity should override this method
	 * and return the final subclass.
	 * @param ?array $props
	 * @return null|class-string<ApiEntity> Null if there is no subclass override, class name otherwise
	 */
	public static function getSubClass(?array $props): ?string {
		return null;
	}

	private static function initFields(self $instance, ?array $props): void {
		if ($props !== null) {
			ApiEntity::$fieldTypeMappings[static::class]->mapFields($props, $instance->__int_fields);
		}
		$instance->onCreate();
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

	/**
	 * The API requires all fields to be present, even if they are null. Build a blank array with all fields,
	 * then apply values from the current instance.
	 * @return array
	 */
	public function jsonSerialize(): array {
		$fields = ApiEntity::$fieldTypeMappings[$this::class]?->getFieldNames() ?? [];
		return array_merge(array_fill_keys($fields, null), $this->__int_fields);
	}

}
