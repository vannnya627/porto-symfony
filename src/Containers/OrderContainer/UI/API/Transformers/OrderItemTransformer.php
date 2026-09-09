<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Transformers;

use App\Containers\OrderContainer\Data\Entities\OrderItem;
use App\Containers\OrderContainer\UI\API\Responses\OrderItemResponse;
use App\Ship\Parents\Transformers\Transformer;

final readonly class OrderItemTransformer extends Transformer
{
    public function run(OrderItem $orderItem): OrderItemResponse
    {
        return OrderItemResponse::create($orderItem);
    }
}
