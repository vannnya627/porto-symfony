<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Values;

use App\Ship\Parents\Values\Value;

final readonly class ProductValue extends Value
{
    private function __construct(
        public string $name,
        public string $description,
        public int $price,
    ) {}

    public static function create(string $name, string $description, int $price): self
    {
        return new self(
            name: $name,
            description: $description,
            price: $price,
        );
    }
}
