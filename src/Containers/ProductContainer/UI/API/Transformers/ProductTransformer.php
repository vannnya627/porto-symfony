<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\UI\API\Transformers;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\UI\API\Responses\ProductResponse;
use App\Ship\Parents\Transformers\Transformer;

final readonly class ProductTransformer extends Transformer
{
    public function transform(Product $product): ProductResponse
    {
        return  ProductResponse::create($product);
    }
}
