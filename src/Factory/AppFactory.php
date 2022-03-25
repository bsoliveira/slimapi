<?php

declare(strict_types=1);

namespace App\Factory;

use Slim\App;
use Psr\Log\LoggerInterface;
use App\Middleware\CorsMiddleware;
use Psr\Container\ContainerInterface;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Factory\AppFactory as SlimAppFactory;

class AppFactory
{
    /**
     * Factory
     *
     * @param ContainerInterface $container
     * @return
     */
    public static function create(ContainerInterface $container): App
    {
        // Set PHP timezone.
        date_default_timezone_set($container->get('app.timezone'));

        // Create App and configure the application via container.
        $app = SlimAppFactory::createFromContainer($container);

        // Parse json, form data and xml.
        $app->addBodyParsingMiddleware();

        // Append a Content-Length header to the response.
        $app->add($container->get(ContentLengthMiddleware::class));

        // Enable CORS
        $app->add($container->get(CorsMiddleware::class));

        // Add Routing Middleware.
        $app->addRoutingMiddleware();

        // Add Error Middleware.
        $app->addErrorMiddleware(
            $container->get('app.debug') == "true", // environment variables are of type string.
            true,
            true,
            $container->get(LoggerInterface::class)
        );

        // Route cache data.
        $routeCacheFile = APP_ROOT . '/storage/cache/routes.php';

        if ($container->get('app.optimized') == "true") {
            $app->getRouteCollector()->setCacheFile($routeCacheFile);
        } else {
            if (is_file($routeCacheFile)) {
                unlink($routeCacheFile);
            }
        }

        // Register routes
        $routes = require APP_ROOT . '/config/routes.php';
        $routes($app, $container);

        return $app;
    }
}
