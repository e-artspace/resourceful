<?php

namespace Resourceful\Controller;
use Silex\Application;
use \Resourceful\StoreHelpers\StoreHelpers;


class AbstractResourceController
{
    protected $schema;

    public function __construct($schema=null)
    {
        $this->schema = $schema;
    }

	protected function getStore(Application $app)
    {
		return StoreHelpers::getStoreForSchema($this->schema, $app);
    }
}
