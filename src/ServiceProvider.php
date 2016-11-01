<?php

namespace Resourceful;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Application;
use Silex\Api\BootableProviderInterface;
use Twig_Loader_Filesystem;
use SchemaStore;
use JDesrosiers\Silex\Provider\ContentNegotiationServiceProvider;
use JDesrosiers\Silex\Provider\CorsServiceProvider;
use Resourceful\Stores\FileCache;
use Symfony\Component\Debug\ErrorHandler;

class ServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    const ERROR_HANDLER_PRIORITY = 0;


    public function register(Container $app)
    {
    	assert($app instanceof Application);
		
    	$app->register(new TwigServiceProvider());
		
		// if true, create schema from templates
		$app["createDefault"] = true;
		
		$app['data.dir'] = sys_get_temp_dir() . '/resourceful';
		$app['resourceful.templates.dir'] = __DIR__ . "/../templates";
		
		// create a storage service for data and schema
		$app['data.store'] = function($app) {
			return new FileCache($app['data.dir']);
		};
		
        // JSON Schema application
        $app["schema.cache"] = function () {
            return new SchemaStore();
        };

        $app["uniqid"] = $app->protect(function ($data) {
            return uniqid();
        });
				
    	// JSON/REST application
        $app->register(new ContentNegotiationServiceProvider(), array(
            "conneg.responseFormats" => array("json"),
            "conneg.requestFormats" => array("json"),
            "conneg.defaultFormat" => "json",
        ));
		
		// cors support
        $app->register(new CorsServiceProvider());
		
		// allow to create controllers as a service 
		$app->register(new ServiceControllerServiceProvider());

		//=====================================================		
		// set application middleware
		//=====================================================	
		
		// manage cors
		$app->after($app["cors"]);
    }


    public function boot(Application $app)
    {
    	assert( isset($app["twig.loader"]));

		// ensure that schema route exists
		if( !isset($app['schema.controller'])) {
    		$app->abort(500,'Schema endpoint not found. Do you have mounted a schema?');
    	}
				
		$app["twig.loader"]->addLoader(new Twig_Loader_Filesystem($app['resourceful.templates.dir']));

		// Error Handling
        ErrorHandler::register();
        $app->error(new JsonErrorHandler($app), self::ERROR_HANDLER_PRIORITY);
    }
}
