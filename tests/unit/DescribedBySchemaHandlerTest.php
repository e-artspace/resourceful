<?php

namespace Resourceful\Test;

use Silex\Application;
use Resourceful\DescribedBySchemaHandler;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DescribedBySchemaHandlerTest extends PHPUnit_Framework_TestCase
{

	protected function mkApp($options=array())
	{
    	$app = new Application($options);
		$app->get('/schema/{id}', function(){})->bind('schema');
		$app->flush();
		
		return $app;
	}

    public function testSchemaId()
    {
    	$handler = new DescribedBySchemaHandler('test');
		$this->assertEquals('test', $handler->getSchemaId());
    }

    public function testInvoke()
    {
		$app = $this->mkApp();
		
		$handler = new DescribedBySchemaHandler('test');
		$response = $handler(new Request,new Response, $app);
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Response',$response );
		$this->assertEquals('application/json; profile="/schema/test"',$response->headers->get('Content-Type') );
    }


    public function testInvokeNoargs()
    {
		$app = $this->mkApp();
		
		$handler = new DescribedBySchemaHandler();
		$response = $handler(new Request,new Response, $app);
		$this->assertInstanceOf('Symfony\Component\HttpFoundation\Response',$response );
		$this->assertEquals('application/json; profile="http://json-schema.org/hyper-schema"',$response->headers->get('Content-Type') );
    }
}
