<?php

namespace Resourceful\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class CreateResourceController extends AbstractSchemaValidatedResourceController
{

    public function __invoke(Application $app, Request $request)
    {
    	$store = $this->getStore($app);
    	
        $requestJson = $request->getContent() ?: "{}";
        $data = json_decode($requestJson);
		if(!isset($data->id)) {
			$data->id = $app["uniqid"];
			$requiredUniquenessTesting = false;
		} else {
			$requiredUniquenessTesting = true;
		}
		
        $this->validate($app, $data->id, $data);
		
        $location = $app["url_generator"]->generate($this->schema, array("id" => $data->id));
		
		if ($requiredUniquenessTesting && $store->contains($location)){
			throw new ConflictHttpException("Sorry $location already exists.");
		}
		
        if ($store->save($location, $data) === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to save resource");
        }

        return $app->json($data, 201, array("Location" => $location));
    }
}
