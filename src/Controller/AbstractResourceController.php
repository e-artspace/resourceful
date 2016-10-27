<?php

namespace Resourceful\Controller;
use Silex\Application;


class AbstractResourceController
{
    protected $schema;

    use \Resourceful\StoreHelpers\StoreHelpers;

    public function __construct($schema=null)
    {
        $this->schema = $schema;
    }

	protected function getStore(Application $app)
    {
    	$type = $this->schema?basename($this->schema):'schema';
		
		return $this->getStoreForType($type, $app);
    }
}
