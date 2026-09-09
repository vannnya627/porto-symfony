<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Actions;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Containers\ProductContainer\Tasks\SaveAndCommitProductTask;
use App\Containers\ProductContainer\Values\UpdateProductValue;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Price;
use Throwable;

final readonly class UpdateProductAction extends Action
{
    public function __construct(
        private GetProductByIdTask $getProductByIdTask,
        private SaveAndCommitProductTask $saveProductTask,
    ) {}

    /**
     * @throws Throwable
     */
    public function run(int $productId, UpdateProductValue $value): Product
    {
        $product = $this->getProductByIdTask->run($productId);

        $product->update(
            newName: $value->name,
            newDescription: $value->description,
            newPrice: (null !== $value->price) ? Price::create($value->price) : null,
        );

        $this->saveProductTask->run($product);

        return $product;
    }
}
