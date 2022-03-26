# SLIMAPI

An example web application API using the Slim PHP micro framework.

This project is aimed at PHP students who want to learn about SLim PHP, which is a lightweight and robust micro framework,
and the different ways of structuring your application that it offers.

We build an application that has:
- Full User CRUD.
- PHP-DI Container and Routes with production optimization.
- Light ORM.
- Paging and filtering of results.
- Lightweight and easy validation
- Midware and CORs authentication.
- JWT authentication.
- Error Handler and Logg.

Use this project only for studies and knowledge, we emphasize that IT HAS NOT BEEN TESTED IN PRODUCTION. Suggestions are welcome!


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
DB_DATABASE   = "database"
DB_USERNAME   = "dbusername"
DB_PASSWORD   = "dbpassword"
```

Start the php server.

```sh
$ php -S localhost:8080 -t public public/index.php;
```

## List of Dependencies

- [slim/slim](https://github.com/slimphp/Slim): Slim is a PHP micro-framework that helps you quickly write simple yet powerful web applications and APIs.
- [slim/psr7](https://github.com/slimphp/Slim-Psr7): Strict PSR-7 implementation used by the Slim Framework
- [monolog/monolog](https://github.com/Seldaek/monolog): Monolog - Logging for PHP.
- [php-di/php-di](https://github.com/PHP-DI/PHP-DI): PHP-DI is a dependency injection container meant to be practical, powerful, and framework-agnostic.
- [atlas/query](https://github.com/atlasphp/Atlas.Query): Provides query statement builders and performers for MySQL, Postgres, SQLite, and Microsoft SQL Server backends connected via Atlas.Pdo.
- [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv): Loads environment variables from `.env` to `getenv()`, `$_ENV` and `$_SERVER` automagically.complex data output, the like found in RESTful APIs, and works really well with JSON.
- [vlucas/valitron](https://github.com/vlucas/valitron): Valitron is a simple, minimal and elegant stand-alone validation library with NO dependencies.
- [firebase/php-jwt](https://github.com/firebase/php-jwt): A simple library to encode and decode JSON Web Tokens (JWT) in PHP.
- [neomerx/cors-psr7](https://github.com/neomerx/cors-psr7): This package has framework agnostic Cross-Origin Resource Sharing (CORS) implementation. 


## Routes

Home
```bash
GET localhost:8080

200 OK
{
    "message":"Welcome to Slimapi!"
}
```

Auth
```bash
POST /auth/login
Accept: application/json
Content-Type: application/json
{
  "email": "admin@domain.com",
  "password": "password"
}

200 OK
{
    "user":{...},
    "token":"..."
}
```

User index
```bash
GET /users
Accept: application/json
Authorization: Bearer eyJ...


200 OK
{
    "data":[
        {user...}
        {user...}
    ],
    "currentPage":1,
    "lastPage":1,
    "perPage":200,
    "total":3
}
```

User store
```bash
POST /users 
Accept: application/json
Authorization: Bearer eyJ...
Content-Type: application/json
{
  "username":"User02", 
  "email":"user02@domain.com", 
  "password":"password"
}

201 Created
{
    "id":4,
    ...
    ...
}
```

User show
```bash
GET /users/1 
Accept: application/json
Authorization: Bearer eyJ...

200 OK
{"id":1,..}
```

User update
```bash
PUT /users/1
Accept: application/json
Authorization: Bearer eyJ...
Content-Type: application/json
{
  "username":"User02 Udated", 
  "email":"user02@gmail.com", 
}

200 OK
{"id":2,...}
```

User delete
```bash
DELETE /users/2
Accept: application/json
Authorization: Bearer eyJ0...
Host: localhost:8080

204 No Content
```

Error Not Found
```bash
GET /users/2
Accept: application/json
Authorization: Bearer eyJ...

404 Not Found
{
    "message": "404 Not Found"
}
```

CORS
```bash
OPTIONS /users
Accept: application/json
Authorization: Bearer eyJ0...
Origin: http://localhost:8080
Access-Control-Request-Method: POST

200 OK
access-control-allow-headers: Content-Type, Accept, Origin, Authorization
access-control-allow-methods: GET, POST, PUT, PATCH, DELETE
access-control-allow-origin: http://localhost:8080
access-control-max-age: 86400
vary: Origin
```