<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Managers;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Managers\Interfaces\ProductServerManagerInterface;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Ship\Parents\Managers\Manager;

final readonly class ServerManager extends Manager implements ProductServerManagerInterface
{
    public function __construct(
        private GetProductByIdTask $getProductByIdTask,
    ) {}

    public function getProductById(int $productId): Product
    {
        return $this->getProductByIdTask->run($productId);

    }
}
