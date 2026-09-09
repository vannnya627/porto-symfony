<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Transformers;

use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\UI\API\Responses\OrderResponse;
use App\Ship\Parents\Transformers\Transformer;

final readonly class OrderTransformer extends Transformer
{
    public function __construct(
        private OrderItemTransformer $orderItemTransformer,
    ) {}

    public function run(Order $order): OrderResponse
    {
        return new OrderResponse(
            id: $order->id,
            totalPrice: $order->totalPrice,
            status: $order->status->value,
            orderItems: array_values($order->orderItems->map($this->orderItemTransformer->run(...))->toArray()),
        );
    }
}
