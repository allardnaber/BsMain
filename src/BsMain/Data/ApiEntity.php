<?php

namespace BsMain\Data;

use BsMain\Api\ApiRequest;
use BsMain\Api\BsApiClient;
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

	private array $__int_orig = [];

	/**
	 * Cached empty instances, to efficiently create new objects.
	 * @var self[]
	 */
	private static array $newInstanceTemplates = [];

	public function __construct(?array $props = []) {

		$reflection = new ReflectionClass(static::class);
		foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
			if (isset($this->{$prop->getName()})) {
				$this->__int_fields[$prop->getName()] = $this->{$prop->getName()};
			}
			unset($this->{$prop->getName()});
		}

		if ($props !== null) {
			$this->setPropertyValues($props);
			$this->onCreate();
		}
	}

	public static function newInstance( $fields = []): static {
		if (!isset(self::$newInstanceTemplates[static::class])) {
			self::$newInstanceTemplates[static::class] = new static(null);
		}
		$instance = clone self::$newInstanceTemplates[static::class];
		$instance->setPropertyValues($fields);
		$instance->onCreate();
		return $instance;
	}

	/**
	 * Can be used for postprocessing after creation of object
	 * @return void
	 */
	protected function onCreate(): void {

	}

	private function setPropertyValues($fields): void {
		foreach ($fields as $name => $value) {
			$this->__int_fields[$name] = $value;
		}
	}

	public function __set(string $name, mixed $value): void {
		if (!isset($this->__int_fields[$name]) || $this->__int_fields[$name] !== $value) {
			$this->__int_fields[$name] = $value;
			$this->__int_dirty[$name] = true;
		}
	}

	public function &__get(string $name): mixed {
		if (!isset($this->__int_fields[$name])) {
			$null = null;
			return $null;
		}

		$value = $this->__int_fields[$name];
		if (!isset($this->__int_orig[$name]) && !is_scalar($value) && !is_resource($value) && !is_callable($value)) {
			$this->__int_orig[$name] = is_array($value) ? self::array_clone($value) : clone($value);
		}
		return $this->__int_fields[$name];
	}

	/**
	 * @return string[]
	 */
	protected function getDirtyFields(): array {
		foreach ($this->__int_orig as $key => $value) {
			if (!isset($this->__int_dirty[$key]) && !self::equals($value, $this->__int_fields[$key])) {
				$this->__int_dirty[$key] = true;
			}
		}
		return array_keys($this->__int_dirty);
	}

	protected function resetDirtyState(): void {
		$this->__int_dirty = [];
		$this->__int_orig = [];
	}

	/**
	 * @param static[] $objects
	 * @return mixed
	 */
	public static function getFieldsFromArray(array $objects, string $fieldName): array {
		return array_map(fn(self $obj) => $obj->$fieldName, $objects);
	}

	protected static function array_clone($array) {
		return array_map(function($element) {
			return ((is_array($element))
				? self::array_clone($element)
				: ((is_object($element))
					? clone $element
					: $element
				)
			);
		}, $array);
	}

	protected static function equals(mixed $valueA, mixed $valueB): bool {
		if ($valueA === $valueB) {
			return true;
		} elseif (
			is_scalar($valueA) ||
			is_resource($valueA) ||
			is_callable($valueA) ||
			gettype($valueA) !== gettype($valueB)
		) {
			return false;
		}

		// complex type
		$propsA = is_object($valueA) ? get_object_vars($valueA) : $valueA;
		$propsB = is_object($valueB) ? get_object_vars($valueB) : $valueB;

		$keysA = array_keys($propsA);
		$keysB = array_keys($propsB);
		if (count($keysA) !== count($keysB) || count(array_intersect($keysA, $keysB)) !== count($keysA)) return false;

		foreach ($propsA as $key => $propAValue) {
			if (!self::equals($propAValue, $propsB[$key])) return false;
		}

		return true;
	}

	public function jsonSerialize(): array {
		return $this->__int_fields;
	}

	/**
	 * @param BsApiClient $client
	 * @return ApiRequest<static>
	 */
	public static function get(BsApiClient $client): ApiRequest {
		return new ApiRequest(RequestMethod::GET, static::class, $client);
	}


	/**
	 * @param BsApiClient $client
	 * @return ApiRequest<static>
	 */
	public static function post(BsApiClient $client): ApiRequest {
		return new ApiRequest(RequestMethod::POST, static::class, $client);
	}


	/**
	 * @param BsApiClient $client
	 * @return ApiRequest<static>
	 */
	public static function put(BsApiClient $client): ApiRequest {
		return new ApiRequest(RequestMethod::PUT, static::class, $client);
	}
}
