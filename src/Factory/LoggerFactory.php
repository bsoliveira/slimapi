<?php

declare(strict_types=1);

namespace App\Factory;

use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Monolog\Handler\RotatingFileHandler;

class LoggerFactory
{
    /**
     * Factory
     *
     * @param ContainerInterface $container
     * @return Logger
     */
    public static function create(ContainerInterface $container): Logger
    {
        $name = $container->get('logger.name');
        $path = $container->get('logger.path');
        $maxfiles = $container->get('logger.maxfiles');
        $level = $container->get('logger.level');

        $logger = new Logger($name);
        $logger->pushHandler(new RotatingFileHandler($path, $maxfiles, $level));

        return $logger;
    }
}
