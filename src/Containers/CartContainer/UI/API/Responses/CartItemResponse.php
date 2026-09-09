<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\UI\API\Responses;

final readonly class CartItemResponse
{
    private function __construct(
        public int $productId,
        public string $productName,
        public int $quantity,
    ) {}

    public static function create(int $productId, string $productName, int $quantity): self
    {
        return new self(productId: $productId, productName: $productName, quantity: $quantity);
    }
}
