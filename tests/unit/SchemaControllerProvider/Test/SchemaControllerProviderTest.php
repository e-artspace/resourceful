<?php

namespace Resourceful\SchemaControllerProvider\Test;

use Resourceful\SchemaControllerProvider\SchemaControllerProvider;
use Silex\Application;
use Resourceful\ResourcefulServiceProvider\ResourcefulServiceProvider;
use PHPUnit_Framework_TestCase;

class SchemaControllerProviderTest extends PHPUnit_Framework_TestCase
{

    public function testConnect()
    {
        $app = new Application();
        $app->register(new ResourcefulServiceProvider());
				
		$schemaControllerProvider = new SchemaControllerProvider();
		
		$route = $schemaControllerProvider
			->connect($app)
			->flush()
			->getIterator()
			->current()
		;
		$this->assertEquals('/{id}', $route->getPath());
		$this->assertEquals((array)'GET', $route->getMethods());
    }
}
