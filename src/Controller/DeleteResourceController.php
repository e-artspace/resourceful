<?php

namespace Resourceful\Controller;

use Silex\Application;
use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class DeleteResourceController extends AbstractResourceController
{
    public function __invoke(Application $app, Request $request)
    {
    	$store = $this->getStore($app);
		
        if ($store->delete($request->getRequestURI()) === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to delete resource");
        }

        return Response::create('', Response::HTTP_NO_CONTENT);
    }
}
