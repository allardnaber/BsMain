<?php

namespace BsMain\Exception;

/**
 * Extended Exception that allows for placeholders to be used
 * in translatable error messages.
 */
class BsAppRuntimeException extends \RuntimeException {
	
	private $params = [];
	
	public function addParam($value) {
		$this->params[] = $value;
	}
	
	public function getParams() {
		return $this->params;
	}
}
