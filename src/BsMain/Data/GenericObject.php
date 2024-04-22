<?php

namespace BsMain\Data;

use InvalidArgumentException;

/**
 * Description of GenericObject
 */
abstract class GenericObject {
	protected array $data = [];

	/**
	 * Creates an array of the specified object type from JSON that has already been converted into an associative array.
	 * @param array $json
	 * @return array Array of the specified object type
	 */
	public static function array(array $json): array {
		$result = [];
		foreach ($json as $item) {
			$result[] = static::instance($item);
		}
		return $result;
	}

	public static function instance(?array $json = null): static {
		return new static($json);
	}

	protected function __construct(?array $json = null) {
		$this->data = $json ?? [];
		if ($json !== null) {
			$this->postCreationProcessing();
			$this->verifyDataFields();
		}
	}

	/**
	 * Verifies if the expected fields are set to prevent errors when interacting with the object.s
	 * @return void
	 * @throws InvalidArgumentException If required fields are missing
	 */
	private function verifyDataFields(): void {
		$missing = array_diff($this->getAvailableFields(), array_keys($this->data));
		if (!empty($missing)) {
			throw new InvalidArgumentException(
				sprintf('Creation of %s failed, due to these missing fields: %s', 
					get_class($this), join(', ', $missing))
			);
		}
	}
	
	public function __get($field) {
		if (in_array($field, $this->getAvailableFields())) {
			return $this->data[$field] ?? null;
		} else {
			throw new InvalidArgumentException(
				sprintf('Object of type %s does not have field %s.', get_class($this), $field)
			);
		}
	}
	
	public function __set($field, $value) {
		$this->data[$field] = $value;
	}
	
	/**
	 * Get a JSON representation of this object.
	 * @param bool $includeAllKeys If true, include the keys for fields that
	 *                             have not been set, required for API update calls.
	 * @return string JSON representation of this object.
	 */
	public function getJson(bool $includeAllKeys = false): string {
		$raw = $includeAllKeys
			  // Fill in empty keys with nulls
			? array_merge(array_fill_keys($this->getAvailableFields(), null), $this->data)
			: $this->data;

		$result = [];
		foreach ($raw as $key => $value) {
			$result[$key] = $value instanceof self ? $value->data : $value;
		}
		return json_encode($result);
	}

	/**
	 * Provides the Brightspace unique id for the object type, to be implemented by the object itself.
	 * @return int|null The unique id if available, null otherwise.
	 */
	public function getBrightspaceId(): ?int {
		return null;
	}

	/**
	 * List the fields that are available in this specific type of object.
	 * @return string[] Field names of the current object type.
	 */
	protected abstract function getAvailableFields(): array;
	
	/**
	 * Optionally provide a method that is called immediately after creating
	 * this object. It can be used to instantiate typed values, like an
	 * OrgUnitUser that has a Role and User value.
	 */
	protected function postCreationProcessing(): void {
		
	}

}
