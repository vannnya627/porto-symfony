<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks;

use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Tasks\Task;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

final readonly class CreateJWTTokenTask extends Task
{
    public function __construct(
        private JWTTokenManagerInterface $JWTTokenManager,
    ) {}

    public function run(User $user): string
    {
        return $this->JWTTokenManager->create($user);
    }
}
