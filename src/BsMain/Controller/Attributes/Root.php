<?php

namespace BsMain\Controller\Attributes;

/**
 * The root Route for this application: references the method that should be called upon entering the app.
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class Root {

	/**
	 * The root Route does not have any parameters.
	 */
	public function __construct() {}
}
