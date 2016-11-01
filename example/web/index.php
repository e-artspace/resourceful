<?php
require __DIR__ . "/../../vendor/autoload.php"; 

$app = new \Silex\Application;

$app->register(new Resourceful\ServiceProvider,array(
	'data.dir' => __DIR__ . '/../data'
));

$app->mount("/", new Resourceful\IndexControllerProvider);
$app->mount("/schema", new Resourceful\SchemaControllerProvider);

//here add your restful resources
$app->mount("/foo", new Resourceful\CRUDControllerProvider('foo'));

$app->run();