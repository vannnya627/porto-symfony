<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks;

use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Data\Repositories\Interfaces\UserRepositoryInterface;
use App\Ship\Parents\Tasks\Task;
use App\Ship\ValueObjects\Email;

final readonly class GetUserByEmailTask extends Task
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    public function run(Email $email): User
    {
        return $this->repository->getByEmail($email);
    }
}
