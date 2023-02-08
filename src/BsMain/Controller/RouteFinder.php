<?php

namespace BsMain\Controller;

use Composer\Script\Event;
use DateTime;

class RouteFinder {

	public static function findRoutes(Event $cmd): void {
		
		$cnt = 'Post install/update run at ' . (new DateTime())->format('r');
		$cnt .= "\n".gettype($cmd) . ': ' /*. serialize($cmd)*/ . "\n\n";
		file_put_contents('/tmp/testinstall.log', $cnt);


	}

}