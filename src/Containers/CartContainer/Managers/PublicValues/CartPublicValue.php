<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers\PublicValues;

use App\Ship\Parents\Values\Value;

final readonly class CartPublicValue extends Value
{
    /**
     * @param list<CartItemPublicValue> $items
     */
    private function __construct(
        public int $id,
        public array $items,
    ) {}

    /**
     * @param list<CartItemPublicValue> $items
     */
    public static function create(int $id, array $items): self
    {
        return new self(
            id: $id,
            items: $items,
        );
    }
}
