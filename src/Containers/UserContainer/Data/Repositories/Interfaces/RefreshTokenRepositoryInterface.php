<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Data\Repositories\Interfaces;

use App\Containers\UserContainer\Data\Entities\RefreshToken;
use App\Ship\ValueObjects\Email;

interface RefreshTokenRepositoryInterface
{
    /**
     * @return list<RefreshToken>
     */
    public function findActiveTokensByEmail(Email $email): array;
}
