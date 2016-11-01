<?php

namespace Resourceful\Test;

use Silex\Application;
use Resourceful\ServiceProvider;
use PHPUnit_Framework_TestCase;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $app = new Application();
		$app->register(new ServiceProvider);
		//die( print_r($app,true));
		foreach (array(
			'request.http_port',
			'request.https_port',
			'debug',
			'charset',
			'logger',
			'resolver',
			'argument_metadata_factory',
			'argument_value_resolvers',
			'argument_resolver',
			'kernel',
			'request_stack',
			'dispatcher',
			'callback_resolver',
			'route_class',
			'route_factory',
			'routes_factory',
			'routes',
			'url_generator',
			'request_matcher',
			'request_context',
			'controllers',
			'controllers_factory',
			'routing.listener',
			'exception_handler',
			'twig.options',
			'twig.form.templates',
			'twig.path',
			'twig.templates',
			'twig.app_variable',
			'twig',
			'twig.loader.filesystem',
			'twig.loader.array',
			'twig.loader',
			'twig.environment_factory',
			'createDefault',
			'data.dir',
			'data.store',
			'schema.cache',
			'uniqid',
			'conneg.responseFormats',
			'conneg.requestFormats',
			'conneg.defaultFormat',
			'conneg',
			'cors.allowOrigin',
			'cors.allowMethods',
			'cors.maxAge',
			'cors.allowCredentials',
			'cors.exposeHeaders',
			'cors',
			'resourceful.templates.dir',
		) as $propery) {
			$this->assertTrue(isset($app[$propery]),"$propery is set");
		}
    }


    /**
     * @expectedException \Symfony\Component\Debug\Exception\ContextErrorException
	 * @expectedExceptionMessage this should caputured as exception
     */	
	public function testBoot()
	{
		$ServiceProvider = new ServiceProvider;
		$app = new Application;
		$app->register($ServiceProvider);
		$app->mount("/schema", new \Resourceful\SchemaControllerProvider);
		$ServiceProvider->boot($app);
		trigger_error ( 'this should caputured as exception');
	}


    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */	
	public function testBootNoSchema()
	{
		$ServiceProvider = new ServiceProvider;
		$app = new Application;
		$app->register($ServiceProvider);
		$ServiceProvider->boot($app);
	}

}
