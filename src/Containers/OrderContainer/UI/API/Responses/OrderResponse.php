<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Responses;

final readonly class OrderResponse
{
    /**
     * @param list<OrderItemResponse> $orderItems
     */
    public function __construct(
        public int $id,
        public int $totalPrice,
        public string $status,
        public array $orderItems,
    ) {}
}
