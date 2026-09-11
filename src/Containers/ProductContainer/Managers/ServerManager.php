<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Managers;

use App\Containers\ProductContainer\Managers\Interfaces\ProductServerManagerInterface;
use App\Containers\ProductContainer\Managers\PublicValues\ProductPublicValue;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Containers\ProductContainer\Tasks\GetProductsByIdsTask;
use App\Ship\Parents\Managers\Manager;

final readonly class ServerManager extends Manager implements ProductServerManagerInterface
{
    public function __construct(
        private GetProductByIdTask $getProductByIdTask,
        private GetProductsByIdsTask $getProductsByIdsTask,
    ) {}

    public function getProductById(int $productId): ProductPublicValue
    {
        $product = $this->getProductByIdTask->run($productId);

        return ProductPublicValue::create($product);
    }

    public function getProductsByIds(array $productIds): array
    {
        $products =  $this->getProductsByIdsTask->run($productIds);

        return array_map(ProductPublicValue::create(...), $products);
    }
}
