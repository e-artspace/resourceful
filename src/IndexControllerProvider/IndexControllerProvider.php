<?php

namespace Resourceful\IndexControllerProvider;

use Doctrine\Common\Cache\Cache;
use Resourceful\Controller\GetResourceController;
use Resourceful\Controller\AbstractResourceController;
use Resourceful\ResourcefulServiceProvider\AddSchema;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Twig_Loader_Filesystem;

class IndexControllerProvider extends AbstractResourceController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
		assert(isset($app["resources_factory"]));
		assert(isset($app["twig"]));

		$store = $this->getStore($app);
        $resource = $app["resources_factory"]($this->schema);
        $app["twig.loader"]->addLoader(new Twig_Loader_Filesystem(__DIR__ . "/templates"));
        $app->before(new AddSchema($this->schema, "index"));
        // Generate default Index resource
        $resource->before(function (Request $request, Application $app) use($store){
            $index = $app["url_generator"]->generate("index");
            if (!$store->contains($index)) {
                $store->save($index, json_decode($app["twig"]->render("default.json.twig")));
            }
        });
        $resource->get("/", new GetResourceController('/schema/index'))->bind("index");
        return $resource;
    }
}
