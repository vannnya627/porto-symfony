<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Managers;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Managers\Interfaces\ProductServerManagerInterface;
use App\Ship\Parents\Managers\Manager;

final readonly class ProductClientManager extends Manager
{
    public function __construct(
        private ProductServerManagerInterface $productServerManager,
    ) {}

    /**
     * @param array<int> $productIds
     *
     * @return list<Product>
     */
    public function getProductsByIds(array $productIds): array
    {
        return $this->productServerManager->getProductsByIds($productIds);
    }
}
