<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tasks;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Data\Repositories\Interfaces\ProductRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

final readonly class GetProductsByIdsTask extends Task
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * @param array<int> $productIds
     *
     * @return list<Product>
     */
    public function run(array $productIds): array
    {
        return $this->productRepository->getProductsByIds($productIds);
    }
}
