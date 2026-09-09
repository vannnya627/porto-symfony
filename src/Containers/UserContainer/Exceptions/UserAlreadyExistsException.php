<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Exceptions;

use App\Ship\Parents\Exceptions\Exception;

final class UserAlreadyExistsException extends Exception
{
    public function __construct(private readonly string $email)
    {
        parent::__construct('Пошта вже використовується');
    }

    public function getContext(): array
    {
        return ['email' => $this->email];
    }
}
