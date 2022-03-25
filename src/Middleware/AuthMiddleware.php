<?php

declare(strict_types=1);

namespace App\Middleware;

use Throwable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpForbiddenException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware
{
    /**
     * @var string
     */
    protected string $jwtkey;

    /**
     * Constructor
     *
     * @param string $jwtkey
     */
    public function __construct(string $jwtkey)
    {
        $this->jwtkey = $jwtkey;
    }

    /**
     * Read and Validate the token and set user_id as request attribute.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * 
     * @throws HttpForbiddenException
     */
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $userId = $this->verify($request);

        $request = $request->withAttribute('__user_id', $userId);

        return $handler->handle($request);
    }

    /**
     * Validate a token and return the user id.
     *
     * @param ServerRequestInterface $request
     * @return integer 
     * 
     * @throws HttpForbiddenException
     */
    protected function verify(ServerRequestInterface $request): ?int
    {
        $bearer = $this->fetchBearer($request);

        if (empty($bearer)) {
            throw new HttpForbiddenException($request, "Invalid token bearer");
        }

        try {
            $token = JWT::decode($bearer, new Key($this->jwtkey, 'HS256'));

            return $token->sub;
        } catch (Throwable $e) {
            throw new HttpForbiddenException($request, $e->getMessage());
        }
    }

    /**
     * Returns the token from the request header.
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    protected function fetchBearer(ServerRequestInterface $request): string
    {
        $header = $request->getHeaderLine('Authorization');

        if (empty($header) || substr($header, 0, 7) != 'Bearer ') {
            return '';
        }

        return substr($header, 7);
    }
}
