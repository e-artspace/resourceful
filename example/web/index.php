<?php
require __DIR__ . "/../../vendor/autoload.php";

$app = new Resourceful\Resourceful(array('debug'=>true));

// create a storage service
$app['datastore'] = function($app) {
	return new \Resourceful\FileCache\FileCache(__DIR__ . '/../data');
};

$app->register(new Silex\Provider\TwigServiceProvider());
$app->register(new Resourceful\ResourcefulServiceProvider\ResourcefulServiceProvider(), array(
    "resourceful.store" => 'datastore'
));

$app->mount("/schema", new Resourceful\SchemaControllerProvider\SchemaControllerProvider());
$app->mount("/", new Resourceful\IndexControllerProvider\IndexControllerProvider('/schema/index'));
$app->mount("/foo", new Resourceful\CrudControllerProvider\CrudControllerProvider('/schema/foo'));

// Initialize CORS support
$app->after($app["cors"]);

$app->run();