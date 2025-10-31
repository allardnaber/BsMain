<?php

namespace BsMain\Data;

use BsMain\Api\ApiRequest;
use BsMain\Api\BsApiClient;
use BsMain\Api\Fields\FieldTypeMapping;
use BsMain\Api\RequestMethod;
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

	private static FieldTypeMapping $fieldTypes;

	public function __construct(?array $props = null) {

		$reflection = new ReflectionClass(static::class);
		if (!isset(self::$fieldTypes)) self::$fieldTypes = new FieldTypeMapping($reflection);
		foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			unset($this->{$prop->getName()});
		}

		if ($props !== null) {
			self::$fieldTypes->mapFields($props, $this->__int_fields);
			$this->onCreate();
		}
	}

	public static function newInstance(?array $props = null): static {
		if (!isset(self::$newInstanceTemplates[static::class])) {
			self::$newInstanceTemplates[static::class] = new static(null);
		}
		$instance = clone self::$newInstanceTemplates[static::class];
		if ($props !== null) {
			self::$fieldTypes->mapFields($props, $instance->__int_fields);
			$instance->onCreate();
		}
		return $instance;
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

	/**
	 * @param BsApiClient $client
	 * @return ApiRequest<static>
	 */
	public static function get(BsApiClient $client): ApiRequest {
		return new ApiRequest(RequestMethod::GET, $client, static::class);
	}

	/**
	 * @param BsApiClient $client
	 * @return ApiRequest<static>
	 */
	public static function post(BsApiClient $client): ApiRequest {
		return new ApiRequest(RequestMethod::POST, $client, static::class);
	}

	/**
	 * @param BsApiClient $client
	 * @return ApiRequest<static>
	 */
	public static function put(BsApiClient $client): ApiRequest {
		return new ApiRequest(RequestMethod::PUT, $client, static::class);
	}
}
