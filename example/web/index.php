<?php

require __DIR__ . "/../../vendor/autoload.php";

$app = new Resourceful\Resourceful();
$app["debug"] = true;

$app->register(new Silex\Provider\TwigServiceProvider\TwigServiceProvider());
$app->register(new Resourceful\ResourcefulServiceProvider\ResourcefulServiceProvider(), array(
    "resourceful.schemaStore" => new Resourceful\FileCache\FileCache(__DIR__ . "/../data"),
));

$app["data"] = new Resourceful\FileCache\FileCache(__DIR__ . "/../data");

// Supporting Controllers

$app->mount("/schema", new Resourceful\SchemaControllerProvider\SchemaControllerProvider());
$app->flush();
$app->mount("/", new Resourceful\IndexControllerProvider\IndexControllerProvider($app["data"]));

// Start Registering CRUD Controllers
$app->mount("/foo", new Resourceful\CrudControllerProvider\CrudControllerProvider("foo", $app["data"]));

// End Registering Controllers

// Initialize CORS support
$app->after($app["cors"]);

$app->run();