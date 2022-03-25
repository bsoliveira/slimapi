<?php

use Dotenv\Dotenv;
use DI\ContainerBuilder;

// Defines the root directory of the application.
define('APP_ROOT', dirname(__DIR__));

// Register autoloader.
require APP_ROOT . '/vendor/autoload.php';

// Loads environment variables from .env.
$dotenv = Dotenv::createImmutable(APP_ROOT);
$dotenv->load();

// Build PHP-DI Container instance.
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions(APP_ROOT . '/config/definitions.php');

if (isset($_ENV['APP_OPTIMIZED']) && $_ENV['APP_OPTIMIZED'] === "true") {
    $containerBuilder->enableCompilation(APP_ROOT . '/storage/cache');
} else {
    $containerCacheFile = APP_ROOT . '/storage/cache/CompiledContainer.php';

    if (is_file($containerCacheFile)) {
        unlink($containerCacheFile);
    }
}

return $containerBuilder->build();
