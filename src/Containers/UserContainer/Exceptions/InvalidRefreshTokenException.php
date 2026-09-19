<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Exceptions;

use App\Ship\Parents\Exceptions\Exception;

class InvalidRefreshTokenException extends Exception
{
    public function __construct(private readonly string $refreshToken)
    {
        parent::__construct('Некоректний токен');
    }

    public function getContext(): array
    {
        return ['refreshToken' => $this->refreshToken];
    }
}
