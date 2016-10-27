<?php

namespace Resourceful\SchemaControllerProvider;

use Resourceful\Controller\GetResourceController;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class SchemaControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
    	assert(isset($app["resources_factory"]));
		
        $resource = $app["resources_factory"]("http://json-schema.org/hyper-schema");
		
        $resource->get("/{id}", new GetResourceController)
            ->assert("id", ".+")
            ->bind("schema");

        return $resource;
    }
}
