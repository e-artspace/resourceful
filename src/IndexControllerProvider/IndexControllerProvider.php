<?php

namespace Resourceful\IndexControllerProvider;

use Doctrine\Common\Cache\Cache;
use Resourceful\Controller\GetResourceController;
use Resourceful\ResourcefulServiceProvider\AddSchema;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Twig_Loader_Filesystem;
use \Resourceful\StoreHelpers\StoreHelpers;

class IndexControllerProvider implements ControllerProviderInterface
{
	protected $schema;

    public function __construct($schema)
    {
    	assert( StoreHelpers::getSchemaType($schema)=='index', 'schema type must be "index"');
		
        $this->schema = $schema;
	}
	
    public function connect(Application $app)
    {
		assert(isset($app["resources_factory"]));
		assert(isset($app["twig"]));

		$store = StoreHelpers::getStoreForSchema($this->schema, $app);
		
        $resource = $app["resources_factory"]($this->schema);
        $app["twig.loader"]->addLoader(new Twig_Loader_Filesystem(__DIR__ . "/templates"));
		$addIndexSchema = new AddSchema($this->schema, "index");

        // Generate default Index resource
        $resource->before(function (Request $request, Application $app) use($store){
            $index = $app["url_generator"]->generate("index");
            if (!$store->contains($index)) {
                $store->save($index, json_decode($app["twig"]->render("default.json.twig")));
            }
        });
        $resource->get("/", new GetResourceController($this->schema))
			->before($addIndexSchema)
        	->bind("index");
        return $resource;
    }
}
