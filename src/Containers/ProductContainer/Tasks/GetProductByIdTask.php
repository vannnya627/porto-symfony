<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tasks;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Data\Repositories\Interfaces\ProductRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

readonly class GetProductByIdTask extends Task
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function run(int $productId): Product
    {
        return $this->repository->getById($productId);
    }
}
