<?php

namespace BsMain\Controller;

use BsMain\Controller\Attributes\Root;
use BsMain\Controller\Attributes\Route;
use Composer\Script\Event;
use DateTime;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

/** @noinspection PhpUnused */
class RouteFactory {

	/**
	 * Internal node name for the root route.
	 */
	public const ROOT = '<root>';

	/**
	 * @var array List of methods (classname - method pair) that have a route attached.
	 */
	private array $routes;

	/**
	 * @param string[] $paths
	 */
	public function __construct(array $paths) {

		foreach ($paths as $path) {
			$this->collectRoutes($path);
		}
		$this->finishCollectingRoutes();
	}

	public function save(string $path): void {
		file_put_contents($path, serialize($this));
	}

	/**
	 * Gets an array of known routes with methods attached to them.
	 * @return array[] Classname - method pairs that reference all published routes.
	 */
	public function getRoutes(): array {
		return $this->routes;
	}

	/**
	 * Gets the application root method.
	 * @return array Classname - method pair that indicates the root method.
	 */
	public function getRoot(): array {
		return $this->routes[self::ROOT];
	}

	private function registerRoute(string $route, string $classname, string $method): void {
		if (isset($this->routes[$route])) {
			throw new RuntimeException(
				sprintf('Duplicate route defined for %s: [%s:%s] and [%s:%s]',
					$route, $this->routes[$route][0], $this->routes[$route][1], $classname, $method)
			);
		}
		if (!$this->isValidRoute($route)) {
			throw new RuntimeException(
				sprintf('Invalid route %s for method %s of class %s (it should start with a slash and each ' .
					'part should contain only alphanumeric characters, there is no trailing slash)',
					$route, $classname, $method)
			);
		}

		$this->routes[$route] = [ $classname, $method ];
	}

	private function isValidRoute(string $route): bool {
		return $route === self::ROOT || preg_match('&^(/[a-zA-Z0-9]+)+$&', $route);
	}

	private function collectRoutes(string $path): void {
		// Preload all classes to force parsing
		$dirIterator = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
		$iterIterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);
		foreach ($iterIterator as $file) {
			if (strtolower($file->getExtension()) === 'php') {
				include_once $file->getPathname();
			}
		}

		// Check all classes: if instance of controller, find routes.
		foreach (get_declared_classes() as $classname) {
			if (is_subclass_of($classname, BsBaseController::class)) {
				$this->collectRoutesFromClass($classname);
			}
		}
	}

	private function finishCollectingRoutes(): void {
		// Verify a root route was set.
		if (!isset($this->routes[self::ROOT])) {
			throw new RuntimeException('No root route has been defined for this application');
		}
	}

	private function collectRoutesFromClass(string $classname): void {
		try {
			$class = new ReflectionClass($classname);
			foreach ($class->getMethods() as $method) {
				foreach ($method->getAttributes() as $attribute) {
					if ($attribute->getName() === Root::class || $attribute->getName() === Route::class) {
						$this->processRouteForMethod($class, $method, $attribute);
					}
				}
			}
		}
		catch (ReflectionException $e) {
			// Only happens if class does not exist, which is unlikely as we are coming from defined classes.
			// Skip this class: if the user tries to open a route form this class, it will fall back to the root page.
		}
	}

	private function processRouteForMethod(ReflectionClass $class, ReflectionMethod $method, ReflectionAttribute $attribute): void {
		if (($method->getModifiers() & ReflectionMethod::IS_PUBLIC) === 0) {
			throw new RuntimeException(
				sprintf('Route set for non-public method %s of class %s', $method->getName(), $class->getName())
			);
		}

		if ($attribute->getName() === Root::class) {
			$this->registerRoute(self::ROOT, $class->getName(), $method->getName());
		} else {
			$arguments = $attribute->getArguments();
			if (count($arguments) === 0) {
				throw new RuntimeException(
					sprintf('Route without arguments for method %s of class %s', $method->getName(), $class->getName())
				);
			}
			$this->registerRoute($arguments[0], $class->getName(), $method->getName());
		}
	}


	public static function fromComposer(Event $event): void {
		$paths = [];
		foreach ($event->getComposer()->getPackage()->getAutoload() as $type => $autoloadPaths) {
			$paths += $autoloadPaths;
		}

		$factory = new self($paths);
		$vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
		$factory->saveForComposer($vendorDir);
	}

	public function saveForComposer(string $vendorDir): void {
		$tpl = <<<EOL
<?php
\$GLOBALS['BsMainRouteMap'] = [
%s
];

require './autoload.php';
EOL;

		$entries = [];
		foreach ($this->routes as $route => [ $classname, $method]) {
			$entries[] = sprintf("  '%s' => [ '%s', '%s']" , str_replace("'", "\\'", $route), $classname, $method);
		}

		$output = sprintf($tpl, join(",\n", $entries));

		file_put_contents(sprintf('%s/BsMain.php', $vendorDir), $output);
	}

}