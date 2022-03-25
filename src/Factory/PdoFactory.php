<?php

declare(strict_types=1);

namespace App\Factory;

use PDO;
use Psr\Container\ContainerInterface;

class PdoFactory
{
    /**
     * Factory
     *
     * @param ContainerInterface $container
     * @return PDO
     */
    public static function create(ContainerInterface $container): PDO
    {
        $host = $container->get('pdo.host');
        $port = $container->get('pdo.port');
        $dbname = $container->get('pdo.dbname');
        $charset = $container->get('pdo.charset');
        $user = $container->get('pdo.user');
        $pass = $container->get('pdo.pass');

        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}",
            $user,
            $pass
        );

        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}
