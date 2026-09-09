<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Tasks;

use App\Containers\OrderContainer\Data\Repositories\Interfaces\OrderRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

final readonly class CommitOrderTask extends Task
{
    public function __construct(
        private OrderRepositoryInterface $repository,
    ) {}

    public function run(): void
    {
        $this->repository->commit();
    }
}
