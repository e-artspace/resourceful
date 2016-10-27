<?php

namespace Resourceful\Controller;
use Silex\Application;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AbstractSchemaValidatedResourceController extends AbstractResourceController
{
		
    protected function validate(Application $app, $id, $data)
    {
        if ($id !== $data->id) {
            throw new BadRequestHttpException("The `id` in the body must match the `id` in the URI");
        }
        $schema = $app["json-schema.schema-store"]->get($this->schema);
        $validation = $app["json-schema.validator"]->validate($data, $schema);
        if (!$validation->valid) {
            throw new BadRequestHttpException(json_encode($validation->errors));
        }
    }
}
