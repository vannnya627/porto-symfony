<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Managers\Interfaces\ProductServerManagerInterface;
use App\Ship\Parents\Managers\Manager;

final readonly class ProductClientManager extends Manager
{
    public function __construct(
        private ProductServerManagerInterface $productServerManager,
    ) {}

    public function getProductById(int $productId): Product
    {
        return $this->productServerManager->getProductById($productId);
    }
}
