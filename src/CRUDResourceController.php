<?php

namespace Resourceful;

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Jsv4;

class CRUDResourceController extends ReadResourceController
{

	/**
	 * @return string $schemaUrl
	 */
    protected function validate(Application $app, $resourceId, \stdClass $data)
    {
    	assert( isset($app["schema.cache"]));
		
		if (!isset($data->id) || ($resourceId !== $data->id)) {
            throw new BadRequestHttpException("The `id` in the body must match the `id` in the URI");
        }
		
		$schemaId=$this->getSchemaId();
    	

		$schemaUrl = SchemaHandler::getSchemaUrl($schemaId, $app);
        $schema = $app["schema.cache"]->get($schemaUrl);
        $validation = Jsv4::validate($data, $schema);
        if (!$validation->valid) {
            throw new BadRequestHttpException(json_encode($validation->errors));
        }
		
		return $schemaUrl;
    }
	
	
    public function create(Application $app, Request $request)
    {
    	assert( isset($app['uniqid']));
		
		$schemaId=$this->getSchemaId();
		$datastore = $this->getDatastore($app);

        $requestJson = $request->getContent()?:"{}";
        $data = json_decode($requestJson);
		if(!isset($data->id)) {
			$uniqudFunction = isset($app["$schemaId.uniqid"])?$app["$schemaId.uniqid"]:$app["uniqid"];
			$data->id = $uniqudFunction($data);
		}
        $this->validate($app, $data->id, $data);

        $location = $app["url_generator"]->generate($schemaId, array("id" => $data->id));
		if ($datastore->contains($location)){
			throw new ConflictHttpException("Sorry $location already exists.");
		} elseif ($datastore->save($location, $data) === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to save resource");
        }

        return $app->json($data, 201, array("Location" => $location));
    }

	
    public function update(Application $app, Request $request, $id)
    {
        $requestJson = $request->getContent() ?: "{}";
        $data = json_decode($requestJson);
        $this->validate($app, $id, $data);
		$datastore = $this->getDatastore($app);

		$requestUri = $request->getRequestUri();
        $isCreated = !$datastore->contains($requestUri);
        if ($datastore->save($requestUri, $data) === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to save resource");
        }

		return $app->json($data,$isCreated?201:200);
    }
	
	
    public function delete(Application $app, Request $request)
    {
		$datastore = $this->getDatastore($app);	
        if ($datastore->delete($request->getRequestURI()) === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to delete resource");
        }

        return Response::create('', Response::HTTP_NO_CONTENT);
    }
}

