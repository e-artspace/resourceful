<?php

namespace Resourceful\Test;

use Resourceful\IndexControllerProvider;
use Silex\Application;
use Resourceful\ServiceProvider;
use PHPUnit_Framework_TestCase;
use Silex\Provider\TwigServiceProvider;

class IndexControllerProviderTest extends PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
        $app = new Application();
        $app->register(new ServiceProvider(array(
        	'data.store' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
		)));		
		$indexControllerProvider = new IndexControllerProvider;
		
		$route = $indexControllerProvider
			->connect($app)
			->flush()
			->getIterator()
			->current()
		;
		$this->assertEquals('/', $route->getPath());
		$this->assertEquals((array)'GET', $route->getMethods());
    }
	
	
    public function testConnecThrouhMount()
    {

        $app = new Application();
        $app->register(new ServiceProvider(array(
        	'data.store' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
		)));
		$app->mount("/", new IndexControllerProvider)->flush();
		
		$this->assertEquals('/', $app["url_generator"]->generate('index'));
    }
	
	
    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */	
    public function testConnecWithTwoIndexMount()
    {
        $app = new Application();
        $app->register(new ServiceProvider());
		$app->mount("/myindex", new IndexControllerProvider);
		$app->mount("/another", new IndexControllerProvider);
    }
}
