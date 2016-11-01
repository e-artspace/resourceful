<?php

namespace Resourceful;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class CRUDControllerProvider implements ControllerProviderInterface
{	
	private $schemaId;


	public function getSchemaId()
	{
		return $this->schemaId;
	}


	public function __construct($schemaId)
	{
		assert (!empty($schemaId));
		$this->schemaId=$schemaId;
	}

	
    public function connect(Application $app)
    {
		$routeName=$schemaId=$this->getSchemaId();
		
		if(isset($app["$routeName.controller"])){
			$app->abort(500,"Route $routeName mounted twice");
		}
		$app["$routeName.controller"] = function() use($schemaId){
			return new CRUDResourceController($schemaId);
		};
			
		// define middleware handlers
		$addCrudSchemaHandler = new SchemaHandler($schemaId, "crud");
		$addSchemaProfileHandler = new DescribedBySchemaHandler($schemaId);
		
		// define crud controllers
		$controllers = $app["controllers_factory"];
        $controllers->post("/", "$routeName.controller:create")
			->before($addCrudSchemaHandler)
			->after($addSchemaProfileHandler)
		;
        $controllers->get("/{id}", "$routeName.controller:read")
        	->bind($routeName)
			->before($addCrudSchemaHandler)
			->after($addSchemaProfileHandler)
		;	
        $controllers->put("/{id}", "$routeName.controller:update")
			->before($addCrudSchemaHandler)
			->after($addSchemaProfileHandler)
		;
        $controllers->delete("/{id}","$routeName.controller:delete")
			->before($addCrudSchemaHandler)
		;
		
        return $controllers;
    }
}
