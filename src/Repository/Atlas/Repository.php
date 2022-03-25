<?php

declare(strict_types=1);

namespace App\Repository\Atlas;

use PDO;
use Atlas\Query\Delete;
use Atlas\Query\Insert;
use Atlas\Query\Select;
use Atlas\Query\Update;
use App\Repository\Pagination;

abstract class Repository
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * Constructor
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create the Select Statement.
     *
     * @return Select
     */
    protected function selectQueryBuild(): Select
    {
        return Select::new($this->pdo);
    }

    /**
     * Create the Insert Statement.
     *
     * @return Insert
     */
    protected function insertQueryBuild(): Insert
    {
        return Insert::new($this->pdo);
    }

    /**
     * Create the Update Statement.
     *
     * @return Update
     */
    protected function updateQueryBuild(): Update
    {
        return Update::new($this->pdo);
    }

    /**
     * Create the Delete Statement.
     *
     * @return Delete
     */
    protected function deleteQueryBuild(): Delete
    {
        return Delete::new($this->pdo);
    }

    /**
     * Create the Pagination.
     *
     * @param Select $query
     * @param integer $page
     * @param integer $perPage
     * @return Pagination
     */
    protected function __paginate(Select $query, int $page, int $perPage, string $class): Pagination
    {
        $totalCount = (int) (clone $query)
            ->resetColumns()
            ->columns('COUNT(DISTINCT id)')
            ->fetchValue();

        $totalPages = (int) ceil($totalCount / $perPage);

        $data = $query
            ->page($page)
            ->perPage($perPage)
            ->fetchObjects($class);

        $pagination = new Pagination();
        $pagination->page = $page;
        $pagination->perPage = $perPage;
        $pagination->totalCount = $totalCount;
        $pagination->totalPages = $totalPages;
        $pagination->data = $data;

        return $pagination;
    }
}
