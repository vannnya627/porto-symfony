<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks\RefreshToken;

use App\Ship\Parents\Tasks\Task;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

readonly class SaveAndCommitRefreshTokenTask extends Task
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
    ) {}

    public function run(RefreshTokenInterface $refreshToken): void
    {
        $this->refreshTokenManager->save($refreshToken);
    }
}
