<?php

declare(strict_types=1);

use SLim\App;
use App\Control\AuthController;
use App\Control\HomeController;
use App\Control\UserController;
use App\Middleware\AuthMiddleware;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;

return function (App $app, ContainerInterface $container) {
    $app->get('/', [HomeController::class, 'index']);

    $app->post('/auth/login', [AuthController::class, 'login']);

    $app->group('/users', function (RouteCollectorProxy $group) {
        $group->get('', [UserController::class, 'index']);
        $group->post('', [UserController::class, 'store']);
        $group->get('/{id:[0-9]+}', [UserController::class, 'show']);
        $group->put('/{id:[0-9]+}', [UserController::class, 'update']);
        $group->delete('/{id:[0-9]+}', [UserController::class, 'delete']);

        // CORS Pre-Flight OPTIONS Request Handler
        $group->options('', [UserController::class, 'preflight']);
        $group->options('/{id:[0-9]+}', [UserController::class, 'preflight']);
    })->add($container->get(AuthMiddleware::class));
};
