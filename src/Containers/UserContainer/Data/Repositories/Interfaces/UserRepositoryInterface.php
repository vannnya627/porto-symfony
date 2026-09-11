<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Data\Repositories\Interfaces;

use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\ValueObjects\Email;

interface UserRepositoryInterface
{
    public function existByEmail(Email $email): bool;

    public function saveAndCommit(User $user): void;
}
