<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Values;

use App\Ship\Parents\Values\Value;

final readonly class RefreshTokenAndJwtTokenValue extends Value
{
    private function __construct(
        public string $token,
        public string $refreshToken,
    ) {}

    public static function create(string $token, string $refreshToken): self
    {
        return new self(token: $token, refreshToken: $refreshToken);
    }
}
