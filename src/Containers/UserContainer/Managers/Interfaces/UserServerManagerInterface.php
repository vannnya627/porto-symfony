<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Managers\Interfaces;

use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\ValueObjects\Email;

interface UserServerManagerInterface
{
    public function getUserByEmail(Email $email): User;
}
