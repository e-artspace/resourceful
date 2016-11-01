<?php

namespace Resourceful\Test;

use PHPUnit_Framework_TestCase;
use Resourceful\CRUDResourceController;
use Resourceful\ServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

class CRUDResourceControllerTest extends PHPUnit_Framework_TestCase
{
	
	private function appFactory()
    {
        $app = new Application;
		$app->register(new ServiceProvider(),array(
        	'data.store' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
		));
        $app->get("/schema/{id}")->bind('schema');
		$app->get("/foo/{id}")->bind('foo');
		$app->flush();
		
		return $app;
    }
	
	private function requestFactory($method, $url,$content)
    {
		return  Request::create(
			$url,							// string $uri, 
			$method,						// string $method = 'GET', 
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
	
	/******************************************
	 * Create
	 ******************************************/

    public function testCreateResourceWithAssignedId()
    {
    	$crudResourceController = new CRUDResourceController("foo");
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
	    $app = $this->appFactory();
		$request = $this->requestFactory('POST','/foo/',$foo);
	
        $response = $crudResourceController->create($app,$request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertEquals("/foo/$foo->id", $response->headers->get("Location"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"$foo->id\"}", $response->getContent());
    }
	
	
    public function testCreateResourceWithGeneratedId()
    {
    	$crudResourceController = new CRUDResourceController('foo');
        $foo = new \stdClass();
	    $app = $this->appFactory();
        $app["uniqid"] = $app->protect(function ($data) {
            return 'abc';
        });
		$request = $this->requestFactory('POST','/foo/',$foo);
	
        $response = $crudResourceController->create($app,$request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertEquals("/foo/abc", $response->headers->get("Location"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"abc\"}", $response->getContent());
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ConflictHttpException
     */	
    public function testCreateWithIdExists()
    {
    	$crudResourceController = new CRUDResourceController('foo');
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
	    $app = $this->appFactory();
        $app['data.store']->method("contains")
            ->willReturn(true);
		$request = $this->requestFactory('POST','/foo/',$foo);
		
		$response = $crudResourceController->create($app,$request);
    }

    /**
     * @expectedException Symfony\Component\Routing\Exception\InvalidParameterException
     */	
    public function testCreateBadRequest()
    {
    	$crudResourceController = new CRUDResourceController('foo');
        $foo = new \stdClass();
		$foo->id = ''; //should throws exception
	    $app = $this->appFactory();
		$request = $this->requestFactory('POST','/foo/',$foo);
	
        $response = $crudResourceController->create($app,$request);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
     */	
    public function testCreateSaveError()
    {
    	$crudResourceController = new CRUDResourceController('foo');
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";
	    $app = $this->appFactory();
        $app['data.store']->method("save")->willReturn(false);
		$request = $this->requestFactory('POST','/foo/',$foo);
	
        $response = $crudResourceController->create($app,$request);
    }
	
	
	/******************************************
	 * Update
	 ******************************************/
	 	
    public function testPutNew()
    {
	    $app = $this->appFactory();
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";

        $app['data.store']->method("contains")
            ->with("/foo/$foo->id")
            ->willReturn(false);

		$request = $this->requestFactory('PUT','/foo/4ee8e29d45851',$foo);
	
		$crudResourceController = new CRUDResourceController("foo");
        $response = $crudResourceController->update($app,$request, $foo->id);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"$foo->id\"}", $response->getContent());
    }

    public function testUpdate()
    {
	    $app = $this->appFactory();
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";

        $app['data.store']->method("contains")
            ->with("/foo/$foo->id")
            ->willReturn(true);

		$request = $this->requestFactory('PUT','/foo/4ee8e29d45851',$foo);
	
		$crudResourceController = new CRUDResourceController("foo");
        $response = $crudResourceController->update($app,$request, $foo->id);
		
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
        $this->assertJsonStringEqualsJsonString("{\"id\":\"$foo->id\"}", $response->getContent());
    }
	

	/**
	 * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
	 */	
    public function testSaveError()
    {
	    $app = $this->appFactory();
        $foo = new \stdClass();
        $foo->id = "4ee8e29d45851";

        $app['data.store']->method("save")->willReturn(false);
		$request = $this->requestFactory('PUT','/foo/4ee8e29d45851',$foo);
		$crudResourceController = new CRUDResourceController("foo");
        $response = $crudResourceController->update($app,$request, $foo->id);
    }
	

	/**
	 * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
	 */	
    public function testIdsDoesNotMatch()
    {
	    $app = $this->appFactory();
        $foo = new \stdClass();
        $foo->id = "bar";

		$request = $this->requestFactory('PUT','/foo/4ee8e29d45851',$foo);
	
		$crudResourceController = new CRUDResourceController("foo");
        $response = $crudResourceController->update($app,$request, '4ee8e29d45851');
    }
	

	
	/******************************************
	 * Delete
	 ******************************************/
    public function testDelete()
    {
    	$crudResourceController = new CRUDResourceController("/schema/foo");
	    $app = $this->appFactory();
		$request = Request::create('/foo/4ee8e29d45851','DELETE');
	
        $response = $crudResourceController->delete($app,$request);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEquals("", $response->getContent());
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
     */	
    public function testDeleteError()
    {
    	$crudResourceController = new CRUDResourceController("/schema/foo");
	    $app = $this->appFactory();
		$request = Request::create('/foo/4ee8e29d45851','DELETE');
        $app['data.store']->method("delete")->willReturn(false);
		
		$response = $crudResourceController->delete($app,$request);
    }
	
}
