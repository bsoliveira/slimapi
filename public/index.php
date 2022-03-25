<?php

use Slim\App;

$container = require __DIR__ . '/../config/bootstrap.php';

$app = $container->get(App::class);
$app->run();