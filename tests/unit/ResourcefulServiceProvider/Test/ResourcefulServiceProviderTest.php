<?php

namespace Resourceful\ResourcefulServiceProvider\Test;

use Silex\Application;
use Resourceful\ResourcefulServiceProvider\ResourcefulServiceProvider;
use PHPUnit_Framework_TestCase;

class ResourcefulServiceProviderTest extends PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $app = new Application();
		$app->register(new ResourcefulServiceProvider);
		foreach (array(
            "resources_factory",
            "uniqid",
		) as $propery) {
			$this->assertTrue(isset($app[$propery]),"$propery is set");
		}
    }
}
