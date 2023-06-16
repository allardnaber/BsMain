<?php

namespace BsMain\Controller;

class ErrorHelper {
	
	private $exception;
	private $controller;

	public const ERROR_HANDLER = [ self::class, 'errorHandler' ];
	
	public function __construct(\Throwable $ex, BsBaseController $controller) {
		$this->exception = $ex;
		$this->controller = $controller;
	}

	public function display() {
		$errorInfo = $this->getErrorInfo();
		http_response_code($errorInfo[2] ?? 500);
		$this->controller->assign('supportEmail', $this->controller->getConfig()['app']['supportEmail']);
		$this->controller->assign('errorType', $errorInfo[0]);
		$this->controller->assign('error', $errorInfo[1]);
		$this->controller->getOutput()->displayError();
	}

	public static function errorHandler($severity, $message, $file, $line): bool {
		if ($severity & ~(E_DEPRECATED | E_STRICT | E_NOTICE)) {
			throw new \ErrorException($message, 0, $severity, $file, $line);
		}
		return true; // skip further error handling
	}
	
	private function getErrorInfo() {
		if ($this->exception instanceof \GuzzleHttp\Exception\RequestException) {
			return [
				$this->controller->getOutput()->getConfigVars('error_api_label'),
				$this->exception->getResponse()->getBody()->getContents(),
				$this->exception->getResponse()->getStatusCode()
			];
		}
		
		if ($this->exception instanceof \BsMain\Exception\BsAppApiException) {
			return [
				$this->controller->getOutput()->getConfigVars('error_api_label'),
				$this->translateChunckedError($this->exception),
				$this->exception->getStatusCode()
			];
		}
		
		if ($this->exception instanceof \BsMain\Exception\BsAppRuntimeException) {
			return [
				$this->controller->getOutput()->getConfigVars('error_app_label'),
				$this->translateErrorMsg($this->exception, $this->exception->getParams())
			];
		}
		
		if ($this->exception instanceof \ErrorException) {
			return [
				sprintf('%s:%s', $this->exception->getFile(), $this->exception->getLine()),
				$this->exception->getMessage()
			];
		}
		
		return [
			get_class($this->exception),
			$this->translateErrorMsg($this->exception)
		];
	}

	private function translateErrorMsg(\Throwable $ex, $params = null) {
		$msg = $ex->getMessage();
		if (substr($msg, 0, 2) === '{#' && substr($msg, -2) === '#}') {
			$key = substr($msg, 2, strlen($msg) - 4);
			$msg = $this->controller->getOutput()->getConfigVars($key) ?? $msg;
		}

		if ($params !== null) {
			$msg = $this->processParams($msg, $params);
		}
		return $msg;
	}
	
	private function processParams($msg, $params, $prefix = '') {
		for ($i = 0; $i < count($params); $i++) {
			$translated = $this->controller->getOutput()->getConfigVars(str_replace(' ', '_', $prefix . $params[$i]));
			if (!empty($translated)) {
				$params[$i] = $translated;
			}
		}

		try {
			return vsprintf($msg, $params);
		} catch (\ValueError $err) {
			return $msg;
		}
	}
	
	private function translateChunckedError(\BsMain\Exception\BsAppApiException $ex) {
		$parts = explode('_', strtolower($ex->getMessage()));
		for ($i = count($parts); $i >= 2; $i--) {
			$key = implode('_', array_slice($parts, 0, $i));
			$translated = $this->controller->getOutput()->getConfigVars($key);
			if (!empty($translated)) {
				return $this->processParams($translated, $ex->getParams(), 'error_');
			}
		}
		return $this->processParams($ex->getMessage(), $ex->getParams());
	}


}
