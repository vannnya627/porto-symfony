<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Transformers;

use App\Containers\OrderContainer\DTOs\OrderItemDTO;
use App\Containers\OrderContainer\UI\API\Responses\OrderItemResponse;
use App\Ship\Parents\Transformers\Transformer;

final readonly class OrderItemTransformer extends Transformer
{
    public function transform(OrderItemDTO $orderItemDTO): OrderItemResponse
    {
        return OrderItemResponse::create($orderItemDTO);
    }
}
