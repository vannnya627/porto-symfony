<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Values;

use App\Ship\Parents\Values\Value;

final readonly class UpdateProductValue extends Value
{
    private function __construct(
        public ?string $name = null,
        public ?string $description = null,
        public ?int $price = null,
    ) {}

    public static function create(?string $name, ?string $description, ?int $price): self
    {
        return new self(
            name: $name,
            description: $description,
            price: $price,
        );
    }
}
