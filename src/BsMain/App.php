<?php

namespace BsMain;

use BsMain\Controller\BsBaseController;
use BsMain\Controller\ErrorHelper;
use BsMain\Controller\RouteFactory;
use BsMain\Template\OutputTemplate;

class App {

	public static function start(array $config): void {
		session_start();
		$output = new OutputTemplate($config['smarty']);
		try {
			set_error_handler(ErrorHelper::ERROR_HANDLER);
			list ($controllerName, $method) = self::getControllerMethod();
			$controller = new $controllerName($output, $config);
			call_user_func([$controller, $method]);
		} catch (\Throwable $ex) {
			$errorController = new BsBaseController($output, $config, true);
			$errorHelper = new ErrorHelper($ex, $errorController);
			$errorHelper->display();
		}
	}

	private static function getControllerMethod() {
		$path = $_SERVER['PATH_INFO'];
		$actions = $GLOBALS[RouteFactory::ACTION_MAPPING_KEY];

		if (isset($actions[$path])) {
			list ($className, $method) = $actions[$path];
			if (class_exists($className) && method_exists($className, $method)) {
				return $actions[$path];
			}
		}
		return $actions[RouteFactory::ROOT];
	}

}