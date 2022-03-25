<?php

declare(strict_types=1);

namespace App\Repository\Atlas;

use App\Model\User;
use App\Repository\Pagination;
use App\Repository\UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function paginate(
        ?string $search,
        int $page,
        int $perPage,
        string $orderBy,
        string $sortBy
    ): Pagination {
        $query = $this->selectQueryBuild()
            ->columns('*')
            ->from('users');

        if ($search) {
            $query
                ->where('username LIKE ', "%{$search}%")
                ->orWhere('email LIKE ', "%{$search}%");
        }

        $query->orderBy("{$orderBy} {$sortBy}");

        return $this->__paginate($query, $page, $perPage, User::class);
    }

    /**
     * @inheritDoc
     */
    public function findById(int $id): ?User
    {
        $userData = $this->selectQueryBuild()
            ->columns('*')
            ->from('users')
            ->where('id = ', $id)
            ->fetchObject(User::class);

        return $userData ? $userData : null;
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email): ?User
    {
        $userData = $this->selectQueryBuild()
            ->columns('*')
            ->from('users')
            ->where('email = ', $email)
            ->fetchObject(User::class);

        return $userData ? $userData : null;
    }

    /**
     * @inheritDoc
     */
    public function insert(array $attributes): int
    {
        $query = $this->insertQueryBuild()
            ->into('users')
            ->column('username', $attributes['username'])
            ->column('email', $attributes['email'])
            ->column('password', $attributes['password'])
            ->column('created_at', date('Y-m-d H:i:s'))
            ->column('updated_at', date('Y-m-d H:i:s'));

        $query->perform();

        return (int) $query->getLastInsertId();
    }

    /**
     * @inheritDoc
     */
    public function update(array $attributes, int $id): void
    {
        $query = $this->updateQueryBuild()
            ->table('users')
            ->column('username', $attributes['username'])
            ->column('email', $attributes['email'])
            ->column('password', $attributes['password'])
            ->column('updated_at', date('Y-m-d H:i:s'))
            ->where('id = ', $id);

        $query->perform();
    }

    /**
     * @inheritDoc
     */
    public function remove(int $id): void
    {
        $query = $this->deleteQueryBuild()
            ->from('users')
            ->where('id = ', $id);

        $query->perform();
    }
}
