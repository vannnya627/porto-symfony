<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Tasks;

use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\Data\Repositories\Interfaces\OrderRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

readonly class SaveAndCommitOrderTask extends Task
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    public function run(Order $order): void
    {
        $this->repository->saveAndCommit($order);
    }
}
