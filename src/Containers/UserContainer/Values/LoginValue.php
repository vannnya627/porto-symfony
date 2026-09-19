<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Values;

use App\Ship\Parents\Values\Value;

final readonly class LoginValue extends Value
{
    private function __construct(
        public int $userId,
        public string $email,
        public string $token,
        public string $refreshToken,
    ) {}

    public static function create(int $userId, string $email, string $token, string $refreshToken): self
    {
        return new self(userId: $userId, email: $email, token: $token, refreshToken: $refreshToken);
    }
}
