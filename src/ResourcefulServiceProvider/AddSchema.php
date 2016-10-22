<?php

namespace Resourceful\ResourcefulServiceProvider;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AddSchema
{
    protected $schema;
    protected $template;
    protected $replacements;

    public function __construct($schema, $template, $replacements = array())
    {
        $this->schema = $schema;
        $this->template = $template;
        $this->replacements = $replacements;
    }

    public function __invoke(Request $request, Application $app)
    {
        if ($app["debug"] && !$app["resourceful.schemaStore"]->contains($this->schema)) {
            $app["resourceful.schemaStore"]->save(
                $this->schema,
                json_decode($app["twig"]->render("$this->template.json.twig", $this->replacements))
            );
        }

        $app["json-schema.schema-store"]->add($this->schema, $app["resourceful.schemaStore"]->fetch($this->schema));
    }
}
