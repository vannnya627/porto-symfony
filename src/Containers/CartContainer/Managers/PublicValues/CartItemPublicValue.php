<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers\PublicValues;

use App\Containers\CartContainer\Data\Entities\CartItem;
use App\Ship\Parents\Values\Value;
use App\Ship\ValueObjects\Quantity;

final readonly class CartItemPublicValue extends Value
{
    private function __construct(
        public int $productId,
        public Quantity $quantity,
    ) {}

    public static function create(CartItem $cartItem): self
    {
        return new self($cartItem->productId, $cartItem->quantity);
    }
}
