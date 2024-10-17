<?php

namespace BsMain\Exception;

/**
 * Extended Exception that allows for placeholders to be used
 * in translatable error messages.
 */
class BsAppRuntimeException extends \RuntimeException {

	/**
	 * @var string[] Parameters for error message
	 */
	private array $params = [];
	
	public function addParam(string $value): void {
		$this->params[] = $value;
	}

	/**
	 * @return string[]
	 */
	public function getParams(): array {
		return $this->params;
	}
}
