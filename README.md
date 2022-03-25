# SLIMAPI

An example web application API using Slim PHP micro framework.


## System requirements

 - PHP 7.4 or newer


## Installation

```sh
$ git clone https://github.com/bsoliveira/slimapi.git

$ cd slimapi

$ chmod -R 755 storage/ 

$ composer install
```

Create the database with the collation `utf8_unicode_ci`, and import the schema: resources/database/schema.sql

Edit the file `env.ini` and set the values according to your development environment.

```sh
DB_HOST       = "127.0.0.1"
DB_PORT       = "3306"
DB_DATABASE   = "php-api"
DB_USERNAME   = "root"
DB_PASSWORD   = "secret"
```

Start the php server.

```sh
$ php -S localhost:8080 -t public public/index.php;
```

## List of Dependencies:

- [slim/slim](https://github.com/slimphp/Slim): Slim is a PHP micro-framework that helps you quickly write simple yet powerful web applications and APIs.
- [slim/psr7](https://github.com/slimphp/Slim-Psr7): Strict PSR-7 implementation used by the Slim Framework
- [monolog/monolog](https://github.com/Seldaek/monolog): Monolog - Logging for PHP.
- [php-di/php-di](https://github.com/PHP-DI/PHP-DI): PHP-DI is a dependency injection container meant to be practical, powerful, and framework-agnostic.
- [atlas/query](https://github.com/atlasphp/Atlas.Query): Provides query statement builders and performers for MySQL, Postgres, SQLite, and Microsoft SQL Server backends connected via Atlas.Pdo.
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv): Loads environment variables from `.env` to `getenv()`, `$_ENV` and `$_SERVER` automagically.complex data output, the like found in RESTful APIs, and works really well with JSON.
- [vlucas/valitron](https://github.com/vlucas/valitron): Valitron is a simple, minimal and elegant stand-alone validation library with NO dependencies.
- [firebase/php-jwt](https://github.com/firebase/php-jwt): A simple library to encode and decode JSON Web Tokens (JWT) in PHP.
- [neomerx/cors-psr7](https://github.com/neomerx/cors-psr7): This package has framework agnostic Cross-Origin Resource Sharing (CORS) implementation. 
