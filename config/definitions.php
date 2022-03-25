<?php

return [
    /**
     * Returns a detailed HTML page with error details and
     * a stack trace. Should be disabled in production.
     */
    'app.debug' => DI\env('APP_DEBUG', 'false'),

    /**
     * Defines that the system should apply the possible optimization,
     * such as compiling the Container and Routes settings.
     *
     * When in development it should be false. The configurations
     * are compiled once and will never be generated again,
     * so some changes in the routes file for example will not be loaded.
     */
    'app.optimized' => DI\env('APP_OPTIMIZED', 'false'),

    /**
     * Define PHP Timezone
     */
    'app.timezone' => DI\env('APP_TIMEZONE', 'UTC'),

    /**
     *  Keyword used in JWT encryption.
     */
    'jwt.key' => DI\env('JWT_KEY'),

    /**
     *  Define Token JWT lifetime.
     */
    'jwt.lifetime' => 7200, // 2 hours in seconds = 2 * 60 * 60

    /**
     * Monolog
     *
     * DEBUG:     Detailed debug information.
     * INFO:      Interesting events.
     * NOTICE:    Normal but significant events.
     * WARNING:   Exceptional occurrences that are not errors.
     * ERROR:     Runtime errors that do not require immediate action but should typically be logged and monitored.         *
     * CRITICAL:  Critical conditions.
     * ALERT:     Action must be taken immediately.
     * EMERGENCY: Emergency: system is unusable.
     */
    'logger.name' => 'slimapi',
    'logger.maxfiles' => 15,
    'logger.path' => APP_ROOT . '/storage/logs/app.log',
    'logger.level' => \Monolog\Logger::DEBUG,

    /**
     *  PDO
     */
    'pdo.host' => DI\env('DB_HOST', 'localhost'),
    'pdo.port' => DI\env('DB_PORT', '3306'),
    'pdo.user' => DI\env('DB_USERNAME'),
    'pdo.pass' => DI\env('DB_PASSWORD'),
    'pdo.dbname' => DI\env('DB_DATABASE'),
    'pdo.charset' => 'utf8',

    /**
     * CORS
     *
     * https://github.com/neomerx/cors-psr7
     */
    'cors.origin.scheme' => 'https',
    'cors.origin.host' => 'api.example.com',
    'cors.origin.port' => 443,
    'cors.check_host' => false,
    'cors.cache_max_age' => 86400,
    'cors.allowed_origins' => ['*'],
    'cors.allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
    'cors.allowed_headers' => ['Content-Type', 'Accept', 'Origin', 'Authorization'],
    'cors.exposed_headers' => [],
    'cors.use_credentials' => false,
    'cors.logger' => null,

    /**
     * Factories
     */
    Slim\App::class => DI\factory([App\Factory\AppFactory::class, 'create']),
    Monolog\Logger::class => DI\factory([App\Factory\LoggerFactory::class, 'create']),
    PDO::class => DI\factory([App\Factory\PdoFactory::class, 'create']),
    Valitron\Validator::class => DI\factory([App\Factory\ValitronFactory::class, 'create']),

    /**
     * Middlewares
     */
    App\Middleware\AuthMiddleware::class => DI\autowire()
        ->constructorParameter('jwtkey', DI\get('jwt.key')),
    App\Middleware\CorsMiddleware::class => DI\autowire()
        ->constructorParameter('serverOriginScheme', DI\get('cors.origin.scheme'))
        ->constructorParameter('serverOriginHost', DI\get('cors.origin.host'))
        ->constructorParameter('serverOriginPort', DI\get('cors.origin.port'))
        ->constructorParameter('checkHost', DI\get('cors.check_host'))
        ->constructorParameter('cacheMaxAge', DI\get('cors.cache_max_age'))
        ->constructorParameter('allowedOrigins', DI\get('cors.allowed_origins'))
        ->constructorParameter('allowedMethods', DI\get('cors.allowed_methods'))
        ->constructorParameter('allowedHeaders', DI\get('cors.allowed_headers'))
        ->constructorParameter('exposedHeaders', DI\get('cors.exposed_headers'))
        ->constructorParameter('isUseCredentials', DI\get('cors.use_credentials'))
        ->constructorParameter('logger', DI\get('cors.logger')),
    Slim\Middleware\ContentLengthMiddleware::class => DI\autowire(),

    /**
     * Controls
     */
    App\Control\HomeController::class => DI\autowire(),
    App\Control\AuthController::class => DI\autowire()
        ->constructorParameter('jwtkey', DI\get('jwt.key'))
        ->constructorParameter('jwtLifetime', DI\get('jwt.lifetime')),
    App\Control\UserController::class => DI\autowire(),

    /**
     * Repositories
     */
    App\Repository\Atlas\UserRepository::class => DI\autowire(),

    /**
     *  Aliases
     */
    Psr\Http\Message\ResponseFactoryInterface::class => DI\create(Slim\Psr7\Factory\ResponseFactory::class),
    Psr\Log\LoggerInterface::class => DI\get(Monolog\Logger::class),
    App\Repository\UserRepositoryInterface::class => DI\get(App\Repository\Atlas\UserRepository::class),
];
