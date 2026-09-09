<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\UI\API\Transformers;

use App\Containers\CartContainer\Data\Entities\CartItem;
use App\Containers\CartContainer\UI\API\Responses\CartItemResponse;
use App\Ship\Parents\Transformers\Transformer;

final readonly class CartItemTransformer extends Transformer
{
    public function run(CartItem $cartItem): CartItemResponse
    {
        $product = $cartItem->product;

        return CartItemResponse::create(
            productId: $product->id,
            productName: $product->name,
            quantity: $cartItem->quantity->value,
        );
    }
}
