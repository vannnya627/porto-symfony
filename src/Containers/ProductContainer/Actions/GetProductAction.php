<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Actions;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Ship\Parents\Actions\Action;

final readonly class GetProductAction extends Action
{
    public function __construct(
        private GetProductByIdTask $getProductByIdTask,
    ) {}

    public function run(int $productId): Product
    {
        return $this->getProductByIdTask->run($productId);
    }
}
