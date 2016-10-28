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
use \Resourceful\StoreHelpers\StoreHelpers;

class CrudControllerProvider implements ControllerProviderInterface
{
    protected $schema;

    public function __construct($schema)
    {
    	assert( !empty(StoreHelpers::getSchemaType($schema)));
		
        $this->schema = $schema;
    }

    public function connect(Application $app)
    {
		assert(isset($app["resources_factory"]));
		assert(isset($app["twig.loader"]));
		
        $resource = $app["resources_factory"]($this->schema);
		
		$type = StoreHelpers::getSchemaType($this->schema);
        $app["twig.loader"]->addLoader(new Twig_Loader_Filesystem(__DIR__ . "/templates"));
        $replacements = array("type" => $type, "title" => ucfirst($type));
		$addCrudSchema = new AddSchema($this->schema, "crud", $replacements);

        $resource->get("/{id}", new GetResourceController($this->schema))
			->before($addCrudSchema)
        	->bind($this->schema);
        $resource->put("/{id}", new PutResourceController($this->schema))
			->before($addCrudSchema);
        $resource->delete("/{id}", new DeleteResourceController($this->schema))
			->before($addCrudSchema);
        $resource->post("/", new CreateResourceController($this->schema))
			->before($addCrudSchema);

        return $resource;
    }
}
