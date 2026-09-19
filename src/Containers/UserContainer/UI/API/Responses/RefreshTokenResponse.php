<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Responses;

use App\Containers\UserContainer\Values\RefreshTokenAndJwtTokenValue;
use App\Ship\Parents\Responses\Response;

final readonly class RefreshTokenResponse extends Response
{
    private function __construct(
        public string $token,
        public string $refreshToken,
    ) {}

    public static function create(RefreshTokenAndJwtTokenValue $value): self
    {
        return new self(token: $value->token, refreshToken: $value->refreshToken);
    }
}
