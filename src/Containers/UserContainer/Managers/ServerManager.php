<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Managers;

use App\Containers\UserContainer\Managers\Interfaces\UserServerManagerInterface;
use App\Containers\UserContainer\Tasks\GetUserByEmailTask;
use App\Ship\Parents\Managers\Manager;
use App\Ship\ValueObjects\Email;
use App\Containers\UserContainer\Data\Entities\User;

final readonly class ServerManager extends Manager implements UserServerManagerInterface
{
    public function __construct(
        private GetUserByEmailTask $getUserByEmailTask,
    ) {}

    public function getUserByEmail(Email $email): User
    {
        return $this->getUserByEmailTask->run($email);

    }
}
