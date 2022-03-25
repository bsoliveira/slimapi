<?php

declare(strict_types=1);

namespace App\Control;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Controller
{
    protected function respondWithData(ResponseInterface $response, $data, int $statusCode = 200): ResponseInterface
    {
        $response->getBody()->write(json_encode($data), JSON_PRETTY_PRINT);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    protected function respondWithError(
        ResponseInterface $response,
        string $message,
        int $statusCode,
        array $errors = []
    ): ResponseInterface {
        $data = ['message' => $message];

        if ($errors) {
            $data['errors'] = $errors;
        }

        return $this->respondWithData($response, $data, $statusCode);
    }

    /**
     * CORS Pre-Flight OPTIONS Request Handler
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function preflight(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // Do nothing here. Just return the response.
        return $response;
    }
}
