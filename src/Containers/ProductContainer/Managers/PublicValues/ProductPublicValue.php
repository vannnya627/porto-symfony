<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Managers\PublicValues;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Ship\Parents\Values\Value;
use App\Ship\ValueObjects\Price;

final readonly class ProductPublicValue extends Value
{
    private function __construct(
        public string $name,
        public string $description,
        public Price $price,
    ) {}

    public static function create(Product $product): self
    {
        return new self(
            name: $product->name,
            description: $product->description,
            price: $product->price,
        );
    }
}
