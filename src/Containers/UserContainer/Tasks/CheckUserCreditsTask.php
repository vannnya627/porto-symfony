<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks;

use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Tasks\Task;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class CheckUserCreditsTask extends Task
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {}

    public function run(User $user, string $password): bool
    {
        return $this->userPasswordHasher->isPasswordValid($user, $password);
    }
}
