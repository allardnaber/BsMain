<?php

namespace BsMain\Exception;

use Throwable;

class BsAppApiException extends BsAppRuntimeException {
	
	private string $dataType, $appName;
	private int $statusCode;
	
	public function __construct(string $method, string $dataType, int $statusCode, ?Throwable $cause = null, string $appName = 'Brightspace') {
		parent::__construct(sprintf('error_api_%d_%s', $statusCode, $method), $statusCode, $cause);
		$this->addParam(strtolower($method));
		$this->addParam($dataType);
		
		$this->dataType= $dataType;
		$this->statusCode = $statusCode;
		$this->appName = $appName;
	}
	
	public function getDataType() {
		return $this->dataType;
	}
	
	public function getStatusCode() {
		return $this->statusCode;
	}

	public function getAppName(): string {
		return $this->appName;
	}
}
