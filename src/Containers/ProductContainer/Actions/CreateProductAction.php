<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Actions;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Tasks\SaveAndCommitProductTask;
use App\Containers\ProductContainer\Values\ProductValue;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Price;

final readonly class CreateProductAction extends Action
{
    public function __construct(
        private SaveAndCommitProductTask $saveProductTask,
    ) {}

    public function run(ProductValue $value): Product
    {
        $product = Product::create($value->name, $value->description, Price::create($value->price));

        $this->saveProductTask->run($product);

        return $product;
    }
}
