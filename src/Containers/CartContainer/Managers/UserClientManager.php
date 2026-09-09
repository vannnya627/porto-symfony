<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers;

use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Managers\Interfaces\UserServerManagerInterface;
use App\Ship\Parents\Managers\Manager;
use App\Ship\ValueObjects\Email;

final readonly class UserClientManager extends Manager
{
    public function __construct(
        private UserServerManagerInterface $userServerManager,
    ) {}

    public function getUserByEmail(Email $email): User
    {
        return $this->userServerManager->getUserByEmail($email);
    }
}
