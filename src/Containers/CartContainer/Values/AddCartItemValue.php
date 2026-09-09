<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Values;

use App\Ship\ValueObjects\Email;

class AddCartItemValue
{
    private function __construct(
        public Email $userEmail,
        public int $productId,
        public int $quantity,
    ) {}

    public static function create(Email $userEmail, int $productId, int $quantity): self
    {
        return new self(userEmail: $userEmail, productId: $productId, quantity: $quantity);
    }
}
