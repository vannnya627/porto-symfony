<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\UI\API\Responses;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Ship\Parents\Responses\Response;

final readonly class ProductResponse extends Response
{
    private function __construct(
        public int $id,
        public string $name,
        public string $description,
        public int $price,
    ) {}

    public static function create(Product $product): self
    {
        return new self(
            id: $product->id,
            name: $product->name,
            description: $product->description,
            price: $product->price->value,
        );
    }
}
