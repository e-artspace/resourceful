<?php

namespace Resourceful\Test;

use Resourceful\ReadResourceController;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

class ReadResourceControllerTest extends PHPUnit_Framework_TestCase
{
	private $app;
	private $request;
	
    protected function setup()
    {
        $this->app = new Application(array(
        	'data.store' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
		));
		$this->request= Request::create('/foo/4ee8e29d45851','GET');
    }

    public function testRead()
    {
    	
        $this->app['data.store']->method("contains")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(true);		
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
        $this->app['data.store']->method("fetch")
            ->with("/foo/4ee8e29d45851")
            ->willReturn($foo);
		
		$readResourceController = new ReadResourceController("foo");
		$response = $readResourceController->read($this->app, $this->request, '4ee8e29d45851');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertJsonStringEqualsJsonString('{"id":"4ee8e29d45851"}', $response->getContent());
    }


    public function testConstructor()
    {
		$readResourceController = new ReadResourceController("foo");
		$this->assertEquals('foo', $readResourceController->getSchemaId());
		
		$readResourceController = new ReadResourceController("bar");
		$this->assertEquals('bar', $readResourceController->getSchemaId());
    }
	

    public function testGetDatastore()
    {
    	$app = new Application(array(
        	'data.store' => 'test',
		));
		$readResourceController = new ReadResourceController("foo");
		$this->assertEquals('test', $readResourceController->getDatastore($app));
    }
	

    public function testGetDatastoreSpecializzed()
    {
    	$app = new Application(array(
        	'data.store' => 'test',
        	'foo.store' => 'priority',
		));
		$readResourceController = new ReadResourceController("foo");
		$this->assertEquals('priority', $readResourceController->getDatastore($app));
    }
	

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */	
    public function testReadNotFound()
    {
        $this->app['data.store']->method("contains")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(false);

		$readResourceController = new ReadResourceController("foo");
		$response = $readResourceController->read($this->app, $this->request, '4ee8e29d45851');
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
     */	
    public function testReadError()
    {
        $this->app['data.store']->method("contains")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(true);

        $this->app['data.store']->method("fetch")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(false);

		$readResourceController = new ReadResourceController("foo");
		$response = $readResourceController->read($this->app,$this->request, '4ee8e29d45851');
    }
}
