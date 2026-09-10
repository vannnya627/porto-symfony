<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\DTOs;

use App\Ship\Parents\DTOs\DTO;
use App\Ship\ValueObjects\Price;
use App\Ship\ValueObjects\Quantity;

final readonly class OrderItemDTO extends DTO
{
    public function __construct(
        public int $productId,
        public string $productName,
        public Quantity $quantity,
        public Price $price,
    ) {}
}
