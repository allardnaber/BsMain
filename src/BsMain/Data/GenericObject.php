<?php

namespace BsMain\Data;

/**
 * Description of GenericObject
 */
abstract class GenericObject {
	protected $data = [];
	
	public static function create($json = null, $isDecoded = false): GenericObject {
		if ($json === null) {
			return new static([]);
		}
		$decoded = $isDecoded ? $json : json_decode($json, true);
		$result = new static($decoded);
		$result->verifyDataFields();
		return $result;
    }

	/**
	 * Creates an array of the specified object types. Object field validation occurs only on the
	 * first instance.
	 * @param string $json
	 * @param bool $isDecoded
	 * @return array
	 */
	public static function createArray($json, $isDecoded = false): array {
		$result = [];
		$decoded = $isDecoded ? $json : json_decode($json, true);
		//$first = true;
		foreach ($decoded as $item) {
			//$obj = new static($item);
			/*$obj = new static($item);
			if ($first) {
				$first = false;
				$obj->verifyDataFields();
			}*/
			$result[] = new static($item, true);
		}
		return $result;
    }

	public function __construct($json = null, $isDecoded = false) {
		if ($json !== null) {
			$decoded = $isDecoded ? $json : json_decode($json, true);

			$this->verifyDataFields();

		}
		$this->postCreationProcessing();
	}

	/*protected function __construct($json) {
		$this->data = $json;
		$this->postCreationProcessing();
	}*/
	
	/**
	 * Verifies if the expected fields are set to prevent errors when interacting with the object.s
	 * @param array $json The decoded object of which the keys will be matched with expected fields.
	 * @throws \Exception If one ore more expected fields are missing from $json.
	 */
	private function verifyDataFields() {
		$missing = array_diff($this->getAvailableFields(), array_keys($this->data));
		if (!empty($missing)) {
			throw new \Exception(
				sprintf('Creation of %s failed, due to these missing fields: %s', 
					get_class($this), join(', ', $missing))
			);
		}
	}
	
	public function __get($field) {
		if (in_array($field, $this->getAvailableFields())) {
			return $this->data[$field] ?? null;
		} else {
			throw new \Exception(
				sprintf('Object of type %s does not have field %s.', get_class($this), $field)
			);
		}
	}
	
	public function __set($field, $value) {
		$this->data[$field] = $value;
	}
	
	/**
	 * Get a JSON representation of this object.
	 * @param type $includeAllKeys If true, include the keys for fields that
	 *                             have not been set, required for API update calls.
	 * @return string JSON representation of this object.
	 */
	public function getJson($includeAllKeys = false) {
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
	 * @return array Field names of the current object type.
	 */
	protected abstract function getAvailableFields();
	
	/**
	 * Optionally provide a method that is called immediately after creating
	 * this object. It can be used to instantiate typed values, like an
	 * OrgUnitUser that has a Role and User value.
	 */
	protected function postCreationProcessing() {
		
	}

}
