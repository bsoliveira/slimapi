<?php

declare(strict_types=1);

namespace App\Repository;

use JsonSerializable;

class Pagination implements JsonSerializable
{
    public int $page;

    public int $perPage;

    public int $totalCount;

    public int $totalPages;

    public array $data;

    public function JsonSerialize()
    {
        return [
            'data' => $this->data,
            'currentPage' => $this->page,
            'lastPage' => $this->totalPages,
            'perPage' => $this->perPage,
            'total' => $this->totalCount,
        ];
    }
}
