<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\User;

interface UserRepositoryInterface
{
    /**
     * Filter and paginate a list of users.
     *
     * @param string $search
     * @param int $page
     * @param int $perPage
     * @param string $orderBy
     * @param string $sortBy
     * @return Pagination
     */
    public function paginate(
        string $search,
        int $page,
        int $perPage,
        string $orderBy,
        string $sortBy
    ): Pagination;

    /**
     * Get a user by id.
     *
     * @param integer $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Get a user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Insert a user into the database
     *
     * @param array $attributes
     * @return integer last insert id 
     */
    public function insert(array $attributes): int;

    /**
     * Update a user in the database.
     *
     * @param array $attributes
     * @param integer $id
     * @return void
     */
    public function update(array $attributes, int $id): void;

    /**
     * Remove a user from the database.
     *
     * @param integer $id
     * @return void
     */
    public function remove(int $id): void;
}
