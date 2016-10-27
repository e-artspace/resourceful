<?php

namespace Resourceful\ResourcefulServiceProvider;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Cache\Cache;

class AddSchema
{
    protected $schema;
    protected $template;
    protected $replacements;

    use \Resourceful\StoreHelpers\StoreHelpers;

    public function __construct($schema, $template, $replacements = array())
    {
        $this->schema = $schema;
        $this->template = $template;
        $this->replacements = $replacements;
    }

    public function __invoke(Request $request, Application $app)
    {
		$store = $this->getStoreForType('schema', $app);
			
        if ($app["debug"] && !$store->contains($this->schema)) {
            $store->save(
                $this->schema,
                json_decode($app["twig"]->render("$this->template.json.twig", $this->replacements))
            );
		}

        $app["json-schema.schema-store"]->add($this->schema, $store->fetch($this->schema));
    }
}
