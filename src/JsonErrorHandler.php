<?php

namespace Resourceful;

use Silex\Application;

class JsonErrorHandler
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function __invoke(\Exception $e, $code)
    {
        $error = array("code" => $e->getCode(), "message" => $e->getMessage());
        if ($this->app["debug"]) {
            $error["trace"] = $e->getTraceAsString();
        }
		
		$response = $this->app->json($error);
		
		return	DescribedBySchemaHandler::apply('error', $this->app, $response );
    }
}
