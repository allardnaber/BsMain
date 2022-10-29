<?php

namespace BsMain\Controller;

use BsMain\Template\OutputTemplate;

 class BsBaseController {
	
	private $output;
	private $config;

	public function __construct(OutputTemplate $output, $config) {
		$this->output = $output;
		$this->config = $config;
	}

	public static function handleRequest($actions, $output, $config) {
		try {
			set_error_handler(['\\BsMain\\Controller\\ErrorHelper', 'errorHandler']);
			$action = self::getControllerMethod($actions);
			$className = $action[0];
			$controller = new $className($output, $config);
			call_user_func([$controller, $action[1]]);
		} catch (\Throwable $ex) {
			$errorcnt = new BsBaseController($output, $config);
			$errorHelper = new ErrorHelper($ex, $errorcnt);
			$errorHelper->display();
		}
	}
	
	private static function getControllerMethod($actions) {
		$path = $_SERVER['PATH_INFO'];
		
		if (isset($actions[$path])) {
			$result = $actions[$path];
			if (class_exists($result[0]) && method_exists($result[0], $result[1])) {
				return $result;
			}
		}
		return $actions['root'];
	}

	public function display($template) {
		$this->output->display($template);
	}
	
	public function assign($var, $value) {
		$this->output->assign($var, $value);
	}
	
	public function getOutput(): OutputTemplate {
		return $this->output;
	}

	public function getConfig() {
		return $this->config;
	}
	

	
}
