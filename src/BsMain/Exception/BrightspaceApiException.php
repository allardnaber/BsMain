<?php

namespace BsMain\Exception;

use BsMain\Api\RequestMethod;

class BrightspaceApiException extends BrightspaceException {
	
	private string $dataType;
	private int $statusCode;

	/**
	 * @var string[] Parameters for error message
	 */
	private array $params = [];

	public function addParam(string $value): void {
		$this->params[] = $value;
	}

	
	public function __construct(RequestMethod $method, string $dataType, int $statusCode) {
		parent::__construct(sprintf('error_api_%d_%s', $statusCode, $method->name));
		$this->addParam(strtolower($method->name));
		$this->addParam($dataType);
		
		$this->dataType= $dataType;
		$this->statusCode = $statusCode;
	}


	/**
	 * @return string[]
	 */
	public function getParams(): array {
		return $this->params;
	}

	public function getDataType() {
		return $this->dataType;
	}
	
	public function getStatusCode() {
		return $this->statusCode;
	}

}
