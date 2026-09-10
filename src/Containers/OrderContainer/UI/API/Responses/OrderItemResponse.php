<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Responses;

use App\Containers\OrderContainer\DTOs\OrderItemDTO;

final readonly class OrderItemResponse
{
    public function __construct(
        public int $productId,
        public string $productName,
        public int $quantity,
        public int $price,
    ) {}

    public static function create(OrderItemDTO $orderItemDTO): self
    {
        return new self(
            productId: $orderItemDTO->productId,
            productName: $orderItemDTO->productName,
            quantity: $orderItemDTO->quantity->value,
            price: $orderItemDTO->price->value,
        );
    }
}
