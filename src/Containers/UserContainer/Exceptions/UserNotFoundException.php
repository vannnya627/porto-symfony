<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Exceptions;

use App\Ship\Parents\Exceptions\Exception;

final class UserNotFoundException extends Exception
{
    public function __construct(private readonly string $email)
    {
        parent::__construct('Користувача не знайдено');
    }

    public function getContext(): array
    {
        return ['email' => $this->email];
    }
}
