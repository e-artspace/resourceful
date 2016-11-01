<?php

namespace Resourceful\Test;

use Resourceful\SchemaControllerProvider;
use Silex\Application;
use Resourceful\ServiceProvider;
use PHPUnit_Framework_TestCase;

class SchemaControllerProviderTest extends PHPUnit_Framework_TestCase
{

    public function testConnectStandalone()
    {
        $app = new Application();
        $app->register(new ServiceProvider());
		
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
	

    public function testConnecThrouhMount()
    {
        $app = new Application();
        $app->register(new ServiceProvider());
		$app->mount("/myschema", new SchemaControllerProvider())->flush();
		
		$this->assertEquals('/myschema/abc', $app["url_generator"]->generate('schema', array("id" => 'abc')));
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */	
    public function testConnecWithTwoSchemaMount()
    {
        $app = new Application();
        $app->register(new ServiceProvider());
		$app->mount("/myschema", new SchemaControllerProvider);
		$app->mount("/another", new SchemaControllerProvider);
    }
}
