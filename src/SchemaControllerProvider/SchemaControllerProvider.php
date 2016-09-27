<?php

namespace Resourceful\SchemaControllerProvider;

use Resourceful\Controller\GetResourceController;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class SchemaControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $resource = $app["resources_factory"]("http://json-schema.org/hyper-schema");

        $resource->get("/{type}", new GetResourceController($app["resourceful.schemaStore"], "application/schema+json"))
            ->assert("type", ".+")
            ->bind("schema");

        return $resource;
    }
}
