<?php

namespace Resourceful\ResourcefulServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Twig_Loader_Filesystem;

class ResourcefulServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{
    const ERROR_HANDLER_PRIORITY = 0;

    public function boot(Application $app)
    {
    	assert( isset($app["twig.loader"]));
		
        // Error Handling
        $schema = $app["url_generator"]->generate("schema", array("id" => "error"));

        $app["twig.loader"]->addLoader(new Twig_Loader_Filesystem(__DIR__ . "/templates"));
        $app->before(new AddSchema($schema, "error"));

        $app->error(function (\Exception $e, $code) use ($app) {
            $app["json-schema.describedBy"] = $app["url_generator"]->generate("schema", array("id" => "error"));
        }, self::ERROR_HANDLER_PRIORITY);
    }

    public function register(Container $app)
    {
        $app["resources_factory"] = $app->protect(new ResourcesFactory($app));

        // CreateResourceController
        $app["uniqid"] = function () {
            return uniqid();
        };
    }
}
