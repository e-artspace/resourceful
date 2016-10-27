<?php

namespace Resourceful\Test;

use Resourceful\Resourceful;
use PHPUnit_Framework_TestCase;
class ResourcefulTest extends PHPUnit_Framework_TestCase
{

    public function testConstruct()
    {
        $app = new Resourceful();
		foreach (array(
            "conneg.responseFormats",
            "conneg.requestFormats",
            "conneg.defaultFormat",
            "cors.allowOrigin",
            "json-schema.correlationMechanism",
            "json-schema.schema-store",
            "json-schema.validator"
		) as $propery) {
			$this->assertTrue(isset($app[$propery]),"$propery is set");
		}
    }
}
