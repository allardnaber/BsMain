<?php

namespace BsMain\Exception;

class BsAppApiException extends BsAppRuntimeException {
	
	private $dataType, $statusCode;
	
	public function __construct($method, $dataType, $statusCode) {
		parent::__construct(sprintf('error_api_%d_%s', $statusCode, $method));
		$this->addParam(strtolower($method));
		$this->addParam($dataType);
		
		$this->dataType= $dataType;
		$this->statusCode = $statusCode;
	}
	
	public function getDataType() {
		return $this->dataType;
	}
	
	public function getStatusCode() {
		return $this->statusCode;
	}
}
