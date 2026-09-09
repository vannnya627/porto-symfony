<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Values;

use App\Ship\Parents\Values\Value;

final readonly class UserValue extends Value
{
    private function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function create(string $email, string $password): self
    {
        return new self(email: $email, password: $password);
    }
}
