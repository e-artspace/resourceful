<?php

namespace Resourceful\StoreHelpers;

use Pimple\Container;
use Doctrine\Common\Cache\Cache;

trait StoreHelpers
{
	protected function getStoreForType($type, Container $app)
	{
    	assert( isset($app['resourceful.store']));
    	$storeName= isset($app["resourceful.store.$type"])?$app["resourceful.store.$type"]:$app['resourceful.store'];
		
		assert(isset($app[$storeName]) && ($app[$storeName] instanceof Cache) );
		return  $app[$storeName];
	}
}