<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Managers\PublicValues;

use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Values\Value;
use App\Ship\ValueObjects\Email;

final readonly class UserPublicValue extends Value
{
    private function __construct(
        public int $id,
        public Email $email,
        public string $password,
    ) {}

    public static function create(User $user): self
    {
        return new self(id: $user->id, email: $user->email, password: $user->password);
    }
}
