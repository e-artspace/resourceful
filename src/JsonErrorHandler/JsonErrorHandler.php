<?php

namespace Resourceful\JsonErrorHandler;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonErrorHandler
{
    protected $app;

    public function __construct($app)
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
