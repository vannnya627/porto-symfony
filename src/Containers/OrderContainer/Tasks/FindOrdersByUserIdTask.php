<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Tasks;

use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\Data\Repositories\Interfaces\OrderRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

final readonly class FindOrdersByUserIdTask extends Task
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    /**
     * @return list<Order>
     */
    public function run(int $userId): array
    {
        return $this->repository->findOrdersByUserId($userId);
    }
}
