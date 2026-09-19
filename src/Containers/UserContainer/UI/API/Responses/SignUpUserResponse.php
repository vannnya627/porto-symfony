<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Responses;

use App\Containers\UserContainer\Values\SignUpValue;
use App\Ship\Parents\Responses\Response;

final readonly class SignUpUserResponse extends Response
{
    private function __construct(
        public int $userId,
        public string $email,
        public string $token,
        public string $refreshToken,
    ) {}

    public static function create(SignUpValue $value): self
    {
        return new self(userId: $value->userId, email: $value->email, token: $value->token, refreshToken: $value->refreshToken);
    }
}
