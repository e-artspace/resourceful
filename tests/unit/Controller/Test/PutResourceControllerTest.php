<?php

namespace Resourceful\Controller\Test;

use Resourceful\Controller\PutResourceController;
use Resourceful\FileCache\FileCache;
use JDesrosiers\Silex\Provider\JsonSchemaServiceProvider;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

class PutResourceControllerTest extends PHPUnit_Framework_TestCase
{
	private $app;

	private function requestFactory( $content)
    {
		return  Request::create(
			'/foo/4ee8e29d45851',			// string $uri, 
			'PUT',							// string $method = 'GET', 
			array(),						// array $parameters = array(), 
			array(),						// array $cookies = array(), 
			array(),						// array $files = array(), 
			array(
	            "HTTP_ACCEPT" => "application/json",
	            "CONTENT_TYPE" => "application/json"
			),								// array $server = array(), 
			json_encode($content)			// string $content = null
		);
    }

    public function setUp()
    {
    	$this->app = new Application(array(
        	'debug' => true,
        	'cachemock' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'resourceful.store' => 'cachemock'
		));
		$this->app->register(new JsonSchemaServiceProvider());
        $this->app["json-schema.schema-store"]->add("/schema/foo", file_get_contents(__DIR__. '/schema/foo.json'));
    }

    public function testCreate()
    {
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";

        $this->app['cachemock']->method("contains")
            ->with("/foo/$foo->id")
            ->willReturn(false);

		$request = $this->requestFactory($foo);
	
		$putResourceController = new PutResourceController("/schema/foo");
        $response = $putResourceController($this->app,$request, $foo->id);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"$foo->id\"}", $response->getContent());
    }

    public function testUpdate()
    {
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";

        $this->app['cachemock']->method("contains")
            ->with("/foo/$foo->id")
            ->willReturn(true);

		$request = $this->requestFactory($foo);
	
		$putResourceController = new PutResourceController("/schema/foo");
        $response = $putResourceController($this->app,$request, $foo->id);
		
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"$foo->id\"}", $response->getContent());
    }
	

	/**
	 * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
	 */	
    public function testSaveError()
    {
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";

        $this->app['cachemock']->method("save")->willReturn(false);
		$request = $this->requestFactory($foo);
		$putResourceController = new PutResourceController("/schema/foo");
        $response = $putResourceController($this->app,$request, $foo->id);
    }
	

	/**
	 * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 */	
    public function testIdsMatch()
    {
        $foo = new \stdClass();
        $foo->id = "bar";

		$request = $this->requestFactory($foo);
	
		$putResourceController = new PutResourceController("/schema/foo");
        $response = $putResourceController($this->app,$request, '4ee8e29d45851');
    }
}
