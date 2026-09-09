<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Data\Repositories\Interfaces;

use App\Containers\OrderContainer\Data\Entities\Order;

interface OrderRepositoryInterface
{
    /**
     * @return list<Order>
     */
    public function findAllByUserIdWithProduct(int $userId): array;

    public function save(Order $order): void;

    public function commit(): void;
}
