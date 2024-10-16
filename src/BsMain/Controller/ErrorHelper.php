<?php

namespace BsMain\Controller;

use BsMain\Exception\BsAppApiException;
use BsMain\Exception\BsAppRuntimeException;
use BsMain\Exception\SafariOauthException;
use ErrorException;
use GuzzleHttp\Exception\RequestException;
use SmartyException;

class ErrorHelper {

	private \Throwable $exception;
	private BsBaseController $controller;

	public const ERROR_HANDLER = [ self::class, 'errorHandler' ];

	public function __construct(\Throwable $ex, BsBaseController $controller) {
		$this->exception = $ex;
		$this->controller = $controller;
	}

	/**
	 * @throws SmartyException
	 */
	public function display(): void {
		$errorInfo = $this->getErrorInfo();
		http_response_code($errorInfo[2] ?? 500);
		$this->controller->assign('supportEmail', $this->controller->getConfig()['app']['supportEmail']);
		$this->controller->assign('errorType', $errorInfo[0]);
		$this->controller->assign('error', $errorInfo[1]);
		if ($this->exception instanceof SafariOauthException) {
			$this->controller->assign('redirectLink', $this->exception->getRedirectLink());
			$this->controller->getOutput()->displaySafariError();
		} else {
			$this->controller->getOutput()->displayError();
		}
	}

	/**
	 * @throws ErrorException
	 */
	public static function errorHandler($severity, $message, $file, $line): bool {
		// Check if error was suppressed (https://www.php.net/manual/en/language.operators.errorcontrol.php)
		// or not relevant to process further.
		$reportLevel = error_reporting();
		if (
			$reportLevel === 0 ||
			$reportLevel === E_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR | E_PARSE
		) {
			return true; // do not show suppressed errors
		}
		throw new ErrorException($message, 0, $severity, $file, $line);
	}

	private function getErrorInfo(): array {
		if ($this->exception instanceof SafariOauthException) {
			return [
				get_class($this->exception),
				$this->exception->getMessage(),
				200
			];
		}

		if ($this->exception instanceof RequestException) {
			return [
				$this->controller->getOutput()->getConfigVars('error_api_label'),
				$this->exception->getResponse()->getBody()->getContents(),
				$this->exception->getResponse()->getStatusCode()
			];
		}

		if ($this->exception instanceof BsAppApiException) {
			return [
				sprintf($this->controller->getOutput()->getConfigVars('error_api_label'), $this->exception->getAppName()),
				$this->translateChunkedError($this->exception),
				$this->exception->getStatusCode()
			];
		}

		if ($this->exception instanceof BsAppRuntimeException) {
			return [
				$this->controller->getOutput()->getConfigVars('error_app_label'),
				$this->translateErrorMsg($this->exception, $this->exception->getParams())
			];
		}

		if ($this->exception instanceof ErrorException) {
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

	private function translateErrorMsg(\Throwable $ex, $params = null): string {
		$msg = $ex->getMessage();
		if (str_starts_with($msg, '{#') && str_ends_with($msg, '#}')) {
			$key = substr($msg, 2, strlen($msg) - 4);
			$msg = $this->controller->getOutput()->getConfigVars($key) ?? $msg;
		}

		if ($params !== null) {
			$msg = $this->processParams($msg, $params);
		}
		return $msg;
	}
	
	private function processParams($msg, $params, $prefix = ''): string {
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

	private function translateChunkedError(BsAppApiException $ex): string {
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
