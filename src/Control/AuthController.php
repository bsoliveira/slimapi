<?php

declare(strict_types=1);

namespace App\Control;

use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface;
use App\Repository\UserRepositoryInterface;
use Slim\Exception\HttpBadRequestException;
use Psr\Http\Message\ServerRequestInterface;

class AuthController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @var string
     */
    protected string $jwtkey;

    /**
     * @var integer
     */
    protected int $jwtLifetime;

    /**
     * Constructor
     *
     * @param string $jwtkey
     * @param integer $jwtLifetime
     */
    public function __construct(UserRepositoryInterface $repository, string $jwtkey, int $jwtLifetime)
    {
        $this->repository = $repository;
        $this->jwtkey = $jwtkey;
        $this->jwtLifetime = $jwtLifetime;
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $inputs = $request->getParsedBody();

        $email = $inputs['email'] ?? "";
        $password = $inputs['password'] ?? "";

        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($password) >= 8) {
            $user = $this->repository->findByEmail($email);

            if ($user && password_verify($password, $user->password)) {
                $payload = [
                    'iat' => time(),
                    'exp' => time() + $this->jwtLifetime,
                    'sub' => $user->id,
                ];

                $token = JWT::encode($payload, $this->jwtkey, 'HS256');

                return $this->respondWithData($response, [
                    'user' => $user,
                    'token' => $token,
                ]);
            }
        }

        throw new HttpBadRequestException($request, "Invalid Credentials");
    }
}
