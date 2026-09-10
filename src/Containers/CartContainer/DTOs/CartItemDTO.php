<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\DTOs;

use App\Ship\Parents\DTOs\DTO;
use App\Ship\ValueObjects\Quantity;

final readonly class CartItemDTO extends DTO
{
    public function __construct(
        public int $productId,
        public string $productName,
        public Quantity $quantity,
    ) {}
}
