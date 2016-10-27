<?php

namespace Resourceful\Controller\Test;

use Resourceful\Controller\CreateResourceController;
use Resourceful\FileCache\FileCache;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\RoutingServiceProvider;
use JDesrosiers\Silex\Provider\JsonSchemaServiceProvider;


class CreateResourceControllerTest extends PHPUnit_Framework_TestCase
{

    private function appFactory()
    {
        $app = new Application(array(
        	'debug' => true,
        	'cachemock' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'resourceful.store' => 'cachemock'
		));
		$app->register(new RoutingServiceProvider());
		$app->register(new JsonSchemaServiceProvider());
        $app->get("/foo/{id}")->bind("/schema/foo");
		$app->flush();
        $app["json-schema.schema-store"]->add("/schema/foo", file_get_contents(__DIR__. '/schema/foo.json'));
		
		return $app;
    }
	
	private function requestFactory( $content)
    {
		return  Request::create(
			'/foo/',						// string $uri, 
			'POST',							// string $method = 'GET', 
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
	

    public function testCreateResourceWithAssignedId()
    {
    	$createResourceController = new CreateResourceController("/schema/foo");
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
	    $app = $this->appFactory();
		$request = $this->requestFactory($foo);
	
        $response = $createResourceController($app,$request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertEquals("/foo/$foo->id", $response->headers->get("Location"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"$foo->id\"}", $response->getContent());
    }
	
	
    public function testCreateResourceWithGeneratedId()
    {
    	$createResourceController = new CreateResourceController("/schema/foo");
        $foo = new \stdClass();
	    $app = $this->appFactory();
		$app['uniqid'] = 'abc';
		$request = $this->requestFactory($foo);
	
        $response = $createResourceController($app,$request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertEquals("/foo/abc", $response->headers->get("Location"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"abc\"}", $response->getContent());
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ConflictHttpException
     */	
    public function testIdExists()
    {
    	$createResourceController = new CreateResourceController("/schema/foo");
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
	    $app = $this->appFactory();
        $app['cachemock']->method("contains")
            ->willReturn(true);
		$request = $this->requestFactory($foo);
		
		$response = $createResourceController($app,$request);
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\InvalidParameterException
     */	
    public function testBadRequest()
    {
    	$createResourceController = new CreateResourceController("/schema/foo");
        $foo = new \stdClass();
		$foo->id = ''; //should thtows exception
	    $app = $this->appFactory();
		$request = $this->requestFactory($foo);
	
        $response = $createResourceController($app,$request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
     */	
    public function testSaveError()
    {
    	$createResourceController = new CreateResourceController("/schema/foo");
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
	    $app = $this->appFactory();
        $app['cachemock']->method("save")->willReturn(false);
		$request = $this->requestFactory($foo);
	
        $response = $createResourceController($app,$request);
    }
}
