<?php
namespace Resourceful;

use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Silex\Application;

class SchemaControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
    	// ensure that SchemaControllerProvider is called once
    	if( isset($app['schema.controller'])){
			$app->abort(501,'Sorry, you can create only one schema endpoint');
		} else {
			$app['schema.controller'] = function(){
				return new ReadResourceController('schema');
			};
		}

        $controllers = $app["controllers_factory"];
		
        $controllers->get("/{id}", 'schema.controller:read')->bind("schema")
			->after(new DescribedBySchemaHandler)
		;
			
        return $controllers;
    }
}
