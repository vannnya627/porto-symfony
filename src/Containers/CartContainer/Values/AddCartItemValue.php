<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Values;

use App\Ship\ValueObjects\Quantity;

class AddCartItemValue
{
    private function __construct(
        public int $userId,
        public int $productId,
        public Quantity $quantity,
    ) {}

    public static function create(int $userId, int $productId, Quantity $quantity): self
    {
        return new self(userId: $userId, productId: $productId, quantity: $quantity);
    }
}
