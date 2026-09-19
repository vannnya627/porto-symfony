<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks\RefreshToken;

use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Tasks\Task;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;

readonly class CreateRefreshTokenTask extends Task
{
    public function __construct(
        private RefreshTokenGeneratorInterface $refreshTokenGenerator,
    ) {}

    public function run(User $user, int $ttl): RefreshTokenInterface
    {
        return $this->refreshTokenGenerator->createForUserWithTtl($user, $ttl);
    }
}
