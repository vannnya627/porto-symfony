<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks;

use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Data\Repositories\Interfaces\UserRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

readonly class SaveAndCommitUserTask extends Task
{
    public function __construct(
        private UserRepositoryInterface $repository,
    ) {}

    public function run(User $user): void
    {
        $this->repository->saveAndCommit($user);
    }
}
