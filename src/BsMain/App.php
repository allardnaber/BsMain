<?php

namespace BsMain;

use BsMain\Configuration\Configuration;
use BsMain\Controller\BsBaseController;
use BsMain\Controller\ErrorHelper;
use BsMain\Controller\RouteFactory;
use BsMain\Template\OutputTemplate;

class App {

	public static function start(array $config): void {
		set_error_handler(ErrorHelper::ERROR_HANDLER);
		$configObj = new Configuration($config);
		SessionManager::create($configObj);
		$output = new OutputTemplate($config['smarty']);
		try {
			list ($controllerName, $method) = self::getControllerMethod();
			$controller = new $controllerName($output, $configObj);
			call_user_func([$controller, $method]);
		} catch (\Throwable $ex) {
			$errorController = new BsBaseController($output, $configObj);
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