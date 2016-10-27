<?php

namespace Resourceful\StoreHelpers\Test;

use PHPUnit_Framework_TestCase;
use Pimple\Container;

class StoreHelperTest extends PHPUnit_Framework_TestCase
{
	use \Resourceful\StoreHelpers\StoreHelpers;
	
	public function testGetStoreForType()
    {
        $app = new Container(array(
        	'cachemock1' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'cachemock2' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'cachemock3' => $this->getMockBuilder("Doctrine\Common\Cache\Cache")->getMock(),
        	'resourceful.store' => 'cachemock1',
        	'resourceful.store.schema' => 'cachemock2',
        	'resourceful.store.index' => 'cachemock3',
        	
		));

		$this->assertEquals($app['cachemock1'], $this->getStoreForType(null, $app));
		$this->assertEquals($app['cachemock1'], $this->getStoreForType('', $app));
		$this->assertEquals($app['cachemock1'], $this->getStoreForType('any', $app));
		$this->assertEquals($app['cachemock2'], $this->getStoreForType('schema', $app));
		$this->assertEquals($app['cachemock3'], $this->getStoreForType('index', $app));
    }
}