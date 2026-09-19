<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks\RefreshToken;

use App\Ship\Parents\Tasks\Task;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

readonly class DeleteAndCommitRefreshTokenTask extends Task
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
    ) {}

    public function run(RefreshTokenInterface $token): bool
    {
        return $this->refreshTokenManager->delete($token) > 0;
    }
}
