<?php
namespace Resourceful;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * TODO: this class colud be extended to allow sub indexed.
 */
class IndexControllerProvider implements ControllerProviderInterface
{
	/**
	 * N.B. the same 'index' string is used to name different concepts-
	 */
    public function connect(Application $app)
    {	
		$controllers = $app["controllers_factory"];
		
		// ensure IndexControllerProvider is called once
		if( isset($app['index.controller'])){
			$app->abort(501, 'Sorry, only one index admited.');
		} else {
			$app['index.controller'] = function(){
				return new ReadResourceController('index'); // here 'index' is a schema id
			};
		}
		
        $controllers->get("/", 'index.controller:read')->bind('index') // here 'index is the route name
			->before(function (Request $request, Application $app){
				// Generate the default index resource on the fly       	
				assert(isset($app['twig']));
				assert(isset($app['data.store']));

				$datastore=$app['data.store'];
				
				if($app["createDefault"]){
		            $resource = $app["url_generator"]->generate('index').'/index'; // first 'index' is the route name, the last is the the resource basename
		            if (!$datastore->contains($resource)) {
		                $datastore->save($resource, json_decode($app["twig"]->render("index.json.twig")));
		            }
				}
	        })
			->before(new SchemaHandler('index')) // here 'index' is a schema id
			->after( new DescribedBySchemaHandler('index')) // here 'index' is a schema id
        ;
			
        return $controllers;
    }
}
