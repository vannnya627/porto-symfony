<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Responses;

use App\Containers\OrderContainer\Data\Entities\OrderItem;

final readonly class OrderItemResponse
{
    public function __construct(
        public int $productId,
        public string $productName,
        public int $quantity,
        public int $price,
    ) {}

    public static function create(OrderItem $orderItem): self
    {
        return new self(
            productId: $orderItem->product->id,
            productName: $orderItem->product->name,
            quantity: $orderItem->quantity->value,
            price: $orderItem->price->value,
        );
    }
}
