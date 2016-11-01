<?php

namespace Resourceful\Test;

use Silex\Application;
use Resourceful\SchemaHandler;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;

class SchemaHandlerTest extends PHPUnit_Framework_TestCase
{

	protected function mkApp($options=array())
	{
    	$app = new Application($options);
		$app->get('/schema/{id}', function(){})->bind('schema');
		$app->flush();
		
		return $app;
	}

    public function testGetSchemaUrl()
    {
		$app = $this->mkApp();
		$this->assertEquals('/schema/test', SchemaHandler::getSchemaUrl('test', $app));
		$this->assertEquals('/schema/test', SchemaHandler::getSchemaUrl('test', $app),"Do it again to us cache");
		$this->assertEquals('/schema/test2', SchemaHandler::getSchemaUrl('test2', $app));
		$this->assertEquals('/schema/test2', SchemaHandler::getSchemaUrl('test2', $app),"Do it again to us cache");
    }
	
	
    public function testRegisterWithNoCreateDefault()
    {
		$app = $this->mkApp(array(
			'schema.cache' => $this->getMockBuilder("SchemaStore")->getMock(),
			'data.store' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
			'twig' =>  $this->getMockBuilder("Twig_Environment")->getMock(),
			'createDefault'	=> false
		));
		
        $app['schema.cache']->method("add")
            ->with('/schema/test', false)
            ->willReturn(null);
		
		$this->assertEquals('/schema/test', SchemaHandler::register($app, 'test'));
    }
	
	
    public function testRegisterWithCreateDefault()
    {
		$app = $this->mkApp(array(
			'schema.cache' => $this->getMockBuilder("SchemaStore")->getMock(),
			'data.store' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
			'twig' =>  $this->getMockBuilder("Twig_Environment")->getMock(),
			'createDefault'	=> true
		));
		
		$app['data.store']->method("contains")
            ->with('/schema/test')
            ->willReturn(false);
				
		$app['twig']->method("render")
            ->with("schema_test.json.twig", array("schemaId" => 'test', "schemaTitle" => 'Test'))
            ->willReturn('{"id":"test"}');
		
		$app['data.store']->method("fetch")
            ->with('/schema/test')
            ->willReturn('{"id":"test"}');
		
        $app['schema.cache']->method("add")
            ->with('/schema/test', '{"id":"test"}')
            ->willReturn(null);
		
		$this->assertEquals('/schema/test', SchemaHandler::register($app, 'test'));
    }
	

    public function testInvoke()
    {
		$app = $this->mkApp(array(
			'schema.cache' => $this->getMockBuilder("SchemaStore")->getMock(),
			'data.store' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
			'twig' =>  $this->getMockBuilder("Twig_Environment")->getMock(),
			'createDefault'	=> false
		));
		
		$handler = new SchemaHandler('test');
		$this->assertNull($handler(new Request, $app),'handler called');
    }
}
