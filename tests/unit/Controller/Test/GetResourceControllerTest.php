<?php

namespace Resourceful\Controller\Test;

use Resourceful\Controller\GetResourceController;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

class GetResourceControllerTest extends PHPUnit_Framework_TestCase
{
	private $app;
	private $request;
	
    protected function setup()
    {
        $this->app = new Application(array(
        	'debug' => true,
        	'cachemock' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'resourceful.store' => 'cachemock'
		));
		$this->request= Request::create('/foo/4ee8e29d45851','GET');
    }

    public function testGet()
    {
    	
        $this->app['cachemock']->method("contains")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(true);		
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
        $this->app['cachemock']->method("fetch")
            ->with("/foo/4ee8e29d45851")
            ->willReturn($foo);
		
		$getResourceController = new GetResourceController("/schema/foo");
		$response = $getResourceController($this->app,$this->request, '4ee8e29d45851');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertJsonStringEqualsJsonString('{"id":"4ee8e29d45851"}', $response->getContent());
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */	
    public function testGetNotFound()
    {
        $this->app['cachemock']->method("contains")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(false);

		$getResourceController = new GetResourceController("/schema/foo");
		$response = $getResourceController($this->app,$this->request, '4ee8e29d45851');
    }
	
    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
     */	
    public function testGetError()
    {
        $this->app['cachemock']->method("contains")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(true);

        $this->app['cachemock']->method("fetch")
            ->with("/foo/4ee8e29d45851")
            ->willReturn(false);

		$getResourceController = new GetResourceController("/schema/foo");
		$response = $getResourceController($this->app,$this->request, '4ee8e29d45851');
    }
}
