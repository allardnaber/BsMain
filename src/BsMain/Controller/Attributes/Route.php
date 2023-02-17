<?php

namespace BsMain\Controller\Attributes;

/**
 * The Route attribute declares the path that discloses the method's functionality to the client.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class Route {

	/**
	 * @param string $path The path which will reference this method.
	 */
	public function __construct(string $path) {}
}
