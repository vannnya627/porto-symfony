<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Managers\Interfaces;

use App\Containers\ProductContainer\Managers\PublicValues\ProductPublicValue;

interface ProductServerManagerInterface
{
    public function getProductById(int $productId): ProductPublicValue;

    /**
     * @param array<int> $productIds
     *
     * @return list<ProductPublicValue>
     */
    public function getProductsByIds(array $productIds): array;
}
