<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Tasks;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Data\Repositories\Interfaces\CartRepositoryInterface;
use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Tasks\Task;

final readonly class FindCartWithItemsAndProductsTask extends Task
{
    public function __construct(
        private CartRepositoryInterface $repository,
    ) {}

    public function run(User $user): ?Cart
    {
        return $this->repository->findCartWithItemsAndProducts($user);
    }
}
