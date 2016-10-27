<?php

namespace Resourceful\Controller\Test;

use Resourceful\Controller\DeleteResourceController;
use PHPUnit_Framework_TestCase;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DeleteResourceControllerTest extends PHPUnit_Framework_TestCase
{

    private function appFactory()
    {
        $app = new Application(array(
        	'debug' => true,
        	'cachemock' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'resourceful.store' => 'cachemock'
		));
		return $app;
    }

    public function testDelete()
    {
    	$deleteResourceController = new DeleteResourceController("/schema/foo");
	    $app = $this->appFactory();
		$request = Request::create('/foo/4ee8e29d45851','DELETE');
	
        $response = $deleteResourceController($app,$request);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertEquals("", $response->getContent());
    }


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
     */	
    public function testDeleteError()
    {
    	$deleteResourceController = new DeleteResourceController("/schema/foo");
	    $app = $this->appFactory();
		$request = Request::create('/foo/4ee8e29d45851','DELETE');
        $app['cachemock']->method("delete")->willReturn(false);
		
		$response = $deleteResourceController($app,$request);
    }
}
