<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks;

use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Tasks\Task;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

readonly class HashPasswordTask extends Task
{
    public function __construct(
        private PasswordHasherFactoryInterface $passwordHasherFactory,
    ) {}

    public function run(string $password): string
    {
        $hasher = $this->passwordHasherFactory->getPasswordHasher(User::class);

        return $hasher->hash($password);
    }
}
