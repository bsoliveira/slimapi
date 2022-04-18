<?php

declare(strict_types=1);

namespace App\Control;

use Valitron\Validator;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Fig\Http\Message\StatusCodeInterface;
use Slim\Exception\HttpNotFoundException;
use App\Repository\UserRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    protected UserRepositoryInterface $repository;

    /**
     * @var Validator
     */
    protected Validator $validator;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param UserRepositoryInterface $repository
     * @param Validator $validator
     * @param LoggerInterface $logger
     * 
     */
    public function __construct(
        UserRepositoryInterface $repository,
        Validator $validator,
        LoggerInterface $logger
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * Displays a list of Users
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $params = $request->getQueryParams();

        /**
         * Validate Query Parameters.
         *
         * see about Valitron Validation: https://github.com/vlucas/valitron
         */
        $v = $this->validator->withData($params);

        // Alternate syntax, add rules on a per-field.
        $v->mapFieldsRules([
            'search' => [
                'alphaNumSpace', // Custom rule defined in Valitron Factory.
            ],
            'orderBy' => [
                ['in', ['id', 'name', 'email', 'created_at']], // White list of sortable attributes. 
            ],
            'sortBy' => [
                ['in', ['asc', 'desc']],
            ],
            'page' => [
                ['min', 1],
            ],
            'perPage' => [
                ['min', 1], ['max', 200],
            ],
        ]);

        if ($v->validate() == false) {
            return $this->respondWithError(
                $response,
                'Invalid Query Parameter',
                StatusCodeInterface::STATUS_BAD_REQUEST,
                $v->errors()
            );
        }

        $pagination = $this->repository->paginate(
            $params['search'] ?? null,
            intval($params['page'] ?? 1),
            intval($params['perPage'] ?? 200),
            $params['orderBy'] ?? 'id',
            $params['sortBy'] ?? 'asc',
        );

        $this->logger->info('A list of users was viewed');

        return $this->respondWithData($response, $pagination);
    }

    /**
     * Shows a User
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];

        $user = $this->repository->findById($id);

        if (!$user) {
            throw new HttpNotFoundException($request, "The user of id: {$id} not found");
        }

        $this->logger->info("the user of id {$id} has been viewed");

        return $this->respondWithData($response, $user);
    }

    /**
     * Create a new User
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $inputs = $request->getParsedBody();

        /**
         * Validate Attributes
         *
         * see about Valitron Validation: https://github.com/vlucas/valitron
         */
        $v = $this->validator->withData($inputs);

        // Alternate syntax, add rules on a per-field.
        $v->mapFieldsRules([
            'username' => [
                'required',
                ['lengthMin', 3],
            ],
            'email' => [
                'required',
                'email',
                ['unique', 'users', 'email'], // Custom rule, checks whether email exists in the database.
            ],
            'password' => [
                'required',
                ['lengthMin', 8],
            ],
        ]);

        if ($v->validate() == false) {
            $this->logger->warning("Invalid attributes for creating user", array_keys($v->errors()));

            return $this->respondWithError(
                $response,
                'Invalid Attribute',
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
                $v->errors()
            );
        }

        // Persiste and return last insert id.
        $id  = $this->repository->insert($inputs);

        $this->logger->info("Created user of id: {$id}");

        $user = $this->repository->findById($id);

        return $this->respondWithData($response, $user, StatusCodeInterface::STATUS_CREATED);
    }

    /**
     * Update a User
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function update(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $inputs = $request->getParsedBody();
        $id = (int) $args['id'];

        $user = $this->repository->findById($id);

        if (!$user) {
            throw new HttpNotFoundException($request, "The user of id: {$id} not found");
        }

        // Validate
        $v = $this->validator->withData($inputs);

        $v->mapFieldsRules([
            'username' => [
                'required',
                ['lengthMin', 3],
            ],
            'email' => [
                'required',
                'email',
                ['unique', 'users', 'email', $id], // Custom rule, checks whether email exists in the database.
            ],
            'password' => [
                'optional',
                'required',
                ['lengthMin', 8],
            ],
        ]);

        if ($v->validate() == false) {
            $this->logger->warning("Invalid attributes for updating user", array_keys($v->errors()));

            return $this->respondWithError(
                $response,
                'Invalid Attribute',
                StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
                $v->errors()
            );
        }

        $user->username = $inputs['username'];
        $user->email = $inputs['email'];

        // This field will only be filled in if the user wants to change the password.
        if (isset($inputs['password']) && strlen($inputs['password']) >= 8) {
            $user->password = password_hash($inputs['password'], PASSWORD_DEFAULT);
        }

        $this->repository->update([
            'username' => $user->username,
            'email' => $user->email,
            'password' => $user->password,
        ], $id);

        $this->logger->info("Updated user id: {$id}");

        $updatedUser = $this->repository->findById($id);

        return $this->respondWithData($response, $updatedUser);
    }

    /**
     * Remove a User
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int) $args['id'];

        $user = $this->repository->findById($id);

        if (!$user) {
            throw new HttpNotFoundException($request, "User of id: {$id} not found.");
        }

        $this->repository->remove($id);

        $this->logger->info("Removed user of id: {$id}");


        // Return 204 no content
        return $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);
    }
}
