<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Managers;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Managers\Interfaces\ProductServerManagerInterface;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Containers\ProductContainer\Tasks\GetProductsByIdsTask;
use App\Ship\Parents\Managers\Manager;

final readonly class ServerManager extends Manager implements ProductServerManagerInterface
{
    public function __construct(
        private GetProductByIdTask $getProductByIdTask,
        private GetProductsByIdsTask $getProductsByIdsTask,
    ) {}

    // TODO зв'язки між контейнерами переробити на ДТО
    public function getProductById(int $productId): Product
    {
        return $this->getProductByIdTask->run($productId);

    }

    /**
     * @param array<int> $productIds
     */
    public function getProductsByIds(array $productIds): array
    {
        return $this->getProductsByIdsTask->run($productIds);
    }
}
