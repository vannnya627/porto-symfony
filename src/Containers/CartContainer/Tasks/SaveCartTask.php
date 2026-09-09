<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Tasks;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Data\Repositories\Interfaces\CartRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

final readonly class SaveCartTask extends Task
{
    public function __construct(
        private CartRepositoryInterface $repository,
    ) {}

    public function run(Cart $cart): void
    {
        $this->repository->save($cart);
    }
}
