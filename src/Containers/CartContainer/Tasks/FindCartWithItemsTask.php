<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Tasks;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Data\Repositories\Interfaces\CartRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

readonly class FindCartWithItemsTask extends Task
{
    public function __construct(
        private CartRepositoryInterface $repository,
    ) {}

    public function run(int $userId): ?Cart
    {
        return $this->repository->findCartWithItems($userId);
    }
}
