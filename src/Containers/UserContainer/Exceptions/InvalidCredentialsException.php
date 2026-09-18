<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Exceptions;

use App\Ship\Parents\Exceptions\Exception;

final class InvalidCredentialsException extends Exception
{
    public function __construct(private readonly mixed $credentials)
    {
        parent::__construct('Дані для входу некоректні');
    }

    public function getContext(): array
    {
        return ['credentials' => $this->credentials];
    }
}
