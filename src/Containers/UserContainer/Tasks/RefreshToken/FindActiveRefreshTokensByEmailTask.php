<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tasks\RefreshToken;

use App\Containers\UserContainer\Data\Entities\RefreshToken;
use App\Containers\UserContainer\Data\Repositories\Interfaces\RefreshTokenRepositoryInterface;
use App\Ship\Parents\Tasks\Task;
use App\Ship\ValueObjects\Email;

final readonly class FindActiveRefreshTokensByEmailTask extends Task
{
    public function __construct(
        private RefreshTokenRepositoryInterface $repository,
    ) {}

    /**
     * @return list<RefreshToken>
     */
    public function run(Email $email): array
    {
        return $this->repository->findActiveTokensByEmail($email);
    }
}
