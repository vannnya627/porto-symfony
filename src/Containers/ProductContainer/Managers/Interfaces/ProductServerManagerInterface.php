<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Managers\Interfaces;

use App\Containers\ProductContainer\Data\Entities\Product;

interface ProductServerManagerInterface
{
    public function getProductById(int $productId): Product;

    /**
     * @param array<int> $productIds
     *
     * @return list<Product>
     */
    public function getProductsByIds(array $productIds): array;
}
