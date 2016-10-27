<?php

namespace Resourceful\Controller;

use Doctrine\Common\Cache\Cache;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class PutResourceController extends AbstractSchemaValidatedResourceController
{
    public function __invoke(Application $app, Request $request, $id)
    {
		$store = $this->getStore($app);
		
        $requestJson = $request->getContent() ?: "{}";
        $data = json_decode($requestJson);

        $this->validate($app, $id, $data);

        $isCreated = !$store->contains($request->getRequestUri());
        if ($store->save($request->getRequestUri(), $data) === false) {
            throw new ServiceUnavailableHttpException(null, "Failed to save resource");
        }

		return $app->json($data,$isCreated?201:200);
    }

}
