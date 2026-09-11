<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tasks;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Data\Repositories\Interfaces\ProductRepositoryInterface;
use App\Ship\Parents\Tasks\Task;

readonly class DeleteProductTask extends Task
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function run(Product $product): void
    {
        $this->repository->removeAndCommit($product);
    }
}
