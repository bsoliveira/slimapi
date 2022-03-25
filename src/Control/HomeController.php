<?php

declare(strict_types=1);

namespace App\Control;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends Controller
{

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $data = [
            'message' => 'Welcome to Slimapi!'
        ];

        return $this->respondWithData($response, $data);
    }
}
