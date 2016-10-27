<?php

namespace Resourceful\IndexControllerProvider\Test;

use Resourceful\IndexControllerProvider\IndexControllerProvider;
use Silex\Application;
use Resourceful\ResourcefulServiceProvider\ResourcefulServiceProvider;
use PHPUnit_Framework_TestCase;
use Silex\Provider\TwigServiceProvider;

class IndexControllerProviderTest extends PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
        $app = new Application(array(
        	'cachemock' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'resourceful.store' => 'cachemock'
		));
        $app->register(new TwigServiceProvider());
        $app->register(new ResourcefulServiceProvider());		
		$indexControllerProvider = new IndexControllerProvider("/schema/index");
		
		$route = $indexControllerProvider
			->connect($app)
			->flush()
			->getIterator()
			->current()
		;
		$this->assertEquals('/', $route->getPath());
		$this->assertEquals((array)'GET', $route->getMethods());
    }
}
