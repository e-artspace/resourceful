<?php

namespace Resourceful\StoreHelpers\Test;

use PHPUnit_Framework_TestCase;
use Pimple\Container;
use \Resourceful\StoreHelpers\StoreHelpers;

class StoreHelpersTest extends PHPUnit_Framework_TestCase
{

	public function testGetStoreForSchema()
    {
        $app = new Container(array(
        	'cachemock1' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'cachemock2' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'cachemock3' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'resourceful.store' => 'cachemock1',
        	'resourceful.store.schema' => 'cachemock2',
        	'resourceful.store.index' => 'cachemock3',
        	
		));
		

		$this->assertEquals($app['cachemock1'], StoreHelpers::getStoreForSchema(null, $app));
		$this->assertEquals($app['cachemock1'], StoreHelpers::getStoreForSchema('/any', $app));
		$this->assertEquals($app['cachemock1'], StoreHelpers::getStoreForSchema('/schema/any', $app));
		$this->assertEquals($app['cachemock2'], StoreHelpers::getStoreForSchema('/schema', $app));
		$this->assertEquals($app['cachemock2'], StoreHelpers::getStoreForSchema('schema', $app));
		$this->assertEquals($app['cachemock3'], StoreHelpers::getStoreForSchema('/index', $app));
    }

	public function testGetSchemaType()
    {
		$this->assertEquals(null, StoreHelpers::getSchemaType(null));
		$this->assertEquals(null, StoreHelpers::getSchemaType(''));
		$this->assertEquals(null, StoreHelpers::getSchemaType('   '));
		$this->assertEquals('abc', StoreHelpers::getSchemaType('abc'));
		$this->assertEquals('abc', StoreHelpers::getSchemaType('/abc'));
		$this->assertEquals('abc', StoreHelpers::getSchemaType('/def/abc'));
		$this->assertEquals('abc', StoreHelpers::getSchemaType('http://example.com/abc'));
		$this->assertEquals('abc', StoreHelpers::getSchemaType('file:////def/abc'));
		$this->assertEquals('abc', StoreHelpers::getSchemaType(':///abc'));
    }
}