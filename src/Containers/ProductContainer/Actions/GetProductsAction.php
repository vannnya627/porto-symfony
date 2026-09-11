<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Actions;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Tasks\GetAllProductTask;
use App\Ship\Parents\Actions\Action;

final readonly class GetProductsAction extends Action
{
    public function __construct(
        private GetAllProductTask $getAllProductTask,
    ) {}

    /**
     * @return list<Product>
     */
    public function run(): array
    {
        return $this->getAllProductTask->run();
    }
}
