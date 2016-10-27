<?php

namespace Resourceful\CrudControllerProvider;

use Resourceful\CrudControllerProvider\CrudControllerProvider;
use Silex\Application;
use Resourceful\ResourcefulServiceProvider\ResourcefulServiceProvider;
use PHPUnit_Framework_TestCase;
use Silex\Provider\TwigServiceProvider;

class CrudControllerProviderTest extends PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
        $app = new Application();
        $app->register(new TwigServiceProvider());
        $app->register(new ResourcefulServiceProvider());		
		$crudControllerProvider = new CrudControllerProvider("/schema/foo");
		
		$routes = $crudControllerProvider->connect($app)->flush();
		
		$this->assertEquals(4, count($routes));
		$expectedRoutes=array(
			array('/{id}', 'GET'),
			array('/{id}', 'PUT'),
			array('/{id}','DELETE'),
			array('/','POST'),
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
}
