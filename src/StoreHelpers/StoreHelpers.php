<?php

namespace Resourceful\StoreHelpers;

use Pimple\Container;
use Doctrine\Common\Cache\Cache;

Class StoreHelpers
{
	/**
	 * @return string | null
	 */
	public static function getSchemaType($schema)
	{
		$type =  @trim(@basename(@parse_url($schema, PHP_URL_PATH)));
		return $type?:null;
	}
	
	/**
	 * @return Cache instance
	 */
	public static function getStoreForSchema($schema, Container $app)
	{
    	assert( isset($app['resourceful.store']));
		
		$type= static::getSchemaType($schema);

    	$storeName= ($type && isset($app["resourceful.store.$type"]))?$app["resourceful.store.$type"]:$app['resourceful.store'];
		
		assert(isset($app[$storeName]) && ($app[$storeName] instanceof Cache) );
		return  $app[$storeName];
	}
}