<?php

namespace Resourceful\IndexControllerProvider;

use Doctrine\Common\Cache\Cache;
use Resourceful\Controller\GetResourceController;
use Resourceful\ResourcefulServiceProvider\AddSchema;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Twig_Loader_Filesystem;

class IndexControllerProvider implements ControllerProviderInterface
{
    private $service;

    public function __construct(Cache $service)
    {
        $this->service = $service;
    }

    public function connect(Application $app)
    {
        $schema = $app["url_generator"]->generate("schema", array("type" => "index"));
        $resource = $app["resources_factory"]($schema);

        $app["twig.loader"]->addLoader(new Twig_Loader_Filesystem(__DIR__ . "/templates"));
        $app->before(new AddSchema($schema, "index"));

        // Generate default Index resource
        $resource->before(function (Request $request, Application $app) {
            $index = $app["url_generator"]->generate("index");
            if (!$this->service->contains($index)) {
                $this->service->save($index, json_decode($app["twig"]->render("default.json.twig")));
            }
        });

        $resource->get("/", new GetResourceController($this->service))->bind("index");

        return $resource;
    }
}
