<?php

namespace Resourceful\Test;

use Resourceful\CRUDControllerProvider;
use Silex\Application;
use Resourceful\ServiceProvider;
use PHPUnit_Framework_TestCase;

class CRUDControllerProviderTest extends PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
        $app = new Application();
        $app->register(new ServiceProvider());		
		$crudControllerProvider = new CRUDControllerProvider("foo");
		
		$routes = $crudControllerProvider->connect($app)->flush();
		
		$this->assertEquals(4, count($routes));
		$expectedRoutes=array(
			array('/','POST'),
			array('/{id}', 'GET'),
			array('/{id}', 'PUT'),
			array('/{id}','DELETE'),
		);
		$iterator = $routes->getIterator();
		while( $iterator->valid() ){
			$route = $iterator->current();
			list($expectedPath,$expectedMethod) = current($expectedRoutes);
			$this->assertEquals($expectedPath, $route->getPath());
			$this->assertEquals((array)$expectedMethod, $route->getMethods());
		    $iterator->next();next($expectedRoutes);
		}
    }
	
	
    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */	
    public function testConnecWithTwoFooMount()
    {
        $app = new Application();
        $app->register(new ServiceProvider());
		$app->mount("/schema", new \Resourceful\IndexControllerProvider);
		$app->mount("/foo", new CRUDControllerProvider('foo'));
		$app->mount("/bar", new CRUDControllerProvider('foo'));
    }
}
