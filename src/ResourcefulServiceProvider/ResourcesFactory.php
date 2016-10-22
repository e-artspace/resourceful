<?php

namespace Resourceful\ResourcefulServiceProvider;

use Pimple\Container;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourcesFactory
{
    protected $app;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function __invoke($schema)
    {
        $resource = $this->app["controllers_factory"];

        $resource->after(function (Request $request, Response $response, Application $app) use ($schema) {
            if ($response->isSuccessful()) {
                $app["json-schema.describedBy"] = $schema;
            }
        });

        return $resource;
    }
}
