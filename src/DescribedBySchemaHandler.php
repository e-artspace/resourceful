<?php

namespace Resourceful;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DescribedBySchemaHandler
{
    private $schemaId;
	
	public function getSchemaId(){ return $this->schemaId;}
	
	public static function apply($schemaId, Application $app, Response $response)
	{
		$handler =new static($schemaId);
		return $handler(new Request,$response,$app);
	}
	
	public function __construct($schemaId=null)
    {
        $this->schemaId = $schemaId;
    }
		
    public function __invoke(Request $request, Response $response, Application $app)
    {
        if ($response->isSuccessful()) {
        	$schema= $this->schemaId?SchemaHandler::getSchemaUrl($this->schemaId, $app):'http://json-schema.org/hyper-schema';
        	$response->headers->set("Content-Type", "application/json; profile=\"$schema\"");
        }

		return $response;
    }
}
