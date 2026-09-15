<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\UI\API\Transformers;

use App\Containers\CartContainer\DTOs\CartItemDTO;
use App\Containers\CartContainer\UI\API\Responses\CartItemResponse;
use App\Ship\Parents\Transformers\Transformer;

final readonly class CartItemTransformer extends Transformer
{
    public function transform(CartItemDTO $cartItemDTO): CartItemResponse
    {
        return CartItemResponse::create(
            productId: $cartItemDTO->productId,
            productName: $cartItemDTO->productName,
            quantity: $cartItemDTO->quantity->value,
        );
    }
}
