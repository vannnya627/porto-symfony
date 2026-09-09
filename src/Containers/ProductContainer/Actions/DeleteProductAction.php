<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Actions;

use App\Containers\ProductContainer\Tasks\DeleteProductTask;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Ship\Parents\Actions\Action;

final readonly class DeleteProductAction extends Action
{
    public function __construct(
        private DeleteProductTask $deleteProductTask,
        private GetProductByIdTask $getProductByIdTask,
    ) {}

    public function run(int $productId): void
    {
        $product = $this->getProductByIdTask->run($productId);

        $this->deleteProductTask->run($product);
    }
}
