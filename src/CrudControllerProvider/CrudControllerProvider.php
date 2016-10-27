<?php

namespace Resourceful\CrudControllerProvider;

use Doctrine\Common\Cache\Cache;
use Resourceful\Controller\CreateResourceController;
use Resourceful\Controller\DeleteResourceController;
use Resourceful\Controller\GetResourceController;
use Resourceful\Controller\PutResourceController;
use Resourceful\ResourcefulServiceProvider\AddSchema;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Twig_Loader_Filesystem;

class CrudControllerProvider implements ControllerProviderInterface
{
    protected $schema;

    public function __construct($schema)
    {
        $this->schema = $schema;
    }

    public function connect(Application $app)
    {
		assert(isset($app["resources_factory"]));
		assert(isset($app["twig.loader"]));
		
        $resource = $app["resources_factory"]($this->schema);
		$type = basename($this->schema);

        $app["twig.loader"]->addLoader(new Twig_Loader_Filesystem(__DIR__ . "/templates"));
        $replacements = array("type" => $type, "title" => ucfirst($type));
        $app->before(new AddSchema($this->schema, "crud", $replacements));

        $resource->get("/{id}", new GetResourceController($this->schema))->bind($this->schema);
        $resource->put("/{id}", new PutResourceController($this->schema));
        $resource->delete("/{id}", new DeleteResourceController($this->schema));
        $resource->post("/", new CreateResourceController($this->schema));

        return $resource;
    }
}
