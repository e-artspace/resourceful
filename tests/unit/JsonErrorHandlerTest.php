<?php

namespace Resourceful\Test;


use PHPUnit_Framework_TestCase;
use Resourceful\JsonErrorHandler;
use Resourceful\ServiceProvider;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Debug\ErrorHandler;

class JsonErrorHandlerTest extends PHPUnit_Framework_TestCase
{
    public function testHandleError()
    {
    	$app = new Application(array('debug'=>true));
        ErrorHandler::register();
        $app->get("/foo", function () {
            throw new NotFoundHttpException("Not Found", null, 4);
        });
        $app->get("/dummyschema/{id}", function () {})->bind('schema');
		
		$app->error(new JsonErrorHandler($app), ServiceProvider::ERROR_HANDLER_PRIORITY);
		$client = new Client($app);

        $client->request("GET", "/foo");
        $response = $client->getResponse();
        $content = json_decode($response->getContent());

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals('application/json; profile="/dummyschema/error"', $response->headers->get("Content-Type"));
        $this->assertEquals(4, $content->code);
        $this->assertEquals("Not Found", $content->message);
        $this->assertInternalType("string", $content->trace);
    }
}
