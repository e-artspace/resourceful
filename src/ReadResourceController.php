<?php

namespace Resourceful;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Doctrine\Common\Cache\Cache;

class ReadResourceController
{
	private $schemaId;

    public function __construct($schemaId)
    {
    	assert (!empty($schemaId));
		
    	$this->schemaId = $schemaId;
    }


	public function getSchemaId()
	{
		return $this->schemaId;
	}

	
	public function getDatastore($app)
	{
		$schemaId= $this->getSchemaId();
		return isset($app["$schemaId.store"])?$app["$schemaId.store"]:$app['data.store'];
	}


    public function read(Application $app, Request $request)
    {
    	$datastore = $this->getDatastore($app);
        if (!$datastore->contains($request->getRequestUri())) {
            throw new NotFoundHttpException("Not Found");
        }

        $resource = $datastore->fetch($request->getRequestUri());
        if ($resource === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to retrieve resource");
        }
		
        return $app->json($resource);
    }
}
