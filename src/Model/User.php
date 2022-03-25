<?php

declare(strict_types=1);

namespace App\Model;

use DateTime;
use JsonSerializable;

class User implements JsonSerializable
{
    public ?int $id;

    public string $username;

    public string $email;

    public ?string $password;

    public ?string $created_at;

    public ?string $updated_at;

    public function JsonSerialize()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'created_at' => DateTime::createFromFormat('Y-m-d H:i:s', $this->created_at)->format(DateTime::ATOM),
            'updated_at' => DateTime::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format(DateTime::ATOM),
        ];
    }
}
