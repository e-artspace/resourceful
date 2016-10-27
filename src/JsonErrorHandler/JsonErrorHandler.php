<?php

namespace Resourceful\JsonErrorHandler;

use Symfony\Component\HttpFoundation\JsonResponse;
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

        return JsonResponse::create($error);
    }
}
