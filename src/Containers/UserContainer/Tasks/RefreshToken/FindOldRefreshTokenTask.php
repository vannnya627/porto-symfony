<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks\RefreshToken;

use App\Ship\Parents\Tasks\Task;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

readonly class FindOldRefreshTokenTask extends Task
{
    public function __construct(
        private RefreshTokenManagerInterface $refreshTokenManager,
    ) {}

    public function run(string $refreshToken): ?RefreshTokenInterface
    {
        return $this->refreshTokenManager->get($refreshToken);
    }
}
