<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Managers;

use App\Containers\ProductContainer\Managers\Interfaces\ProductServerManagerInterface;
use App\Containers\ProductContainer\Managers\PublicValues\ProductPublicValue;
use App\Ship\Parents\Managers\Manager;

readonly class ProductClientManager extends Manager
{
    public function __construct(
        private ProductServerManagerInterface $productServerManager,
    ) {}

    /**
     * @param array<int> $productIds
     *
     * @return list<ProductPublicValue>
     */
    public function getProductsByIds(array $productIds): array
    {
        return $this->productServerManager->getProductsByIds($productIds);
    }
}
