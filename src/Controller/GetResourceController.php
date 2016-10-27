<?php

namespace Resourceful\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class GetResourceController extends AbstractResourceController
{
    public function __invoke(Application $app, Request $request)
    {
		$store = $this->getStore($app);
		
        if (!$store->contains($request->getRequestUri())) {
            throw new NotFoundHttpException("Not Found");
        }

        $resource = $store->fetch($request->getRequestUri());
        if ($resource === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to retrieve resource");
        }
		
        return $app->json($resource);
    }
}
