<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Transformers;

use App\Containers\OrderContainer\DTOs\OrderDTO;
use App\Containers\OrderContainer\UI\API\Responses\OrderResponse;
use App\Ship\Parents\Transformers\Transformer;

final readonly class OrderTransformer extends Transformer
{
    public function __construct(
        private OrderItemTransformer $orderItemTransformer,
    ) {}

    public function run(OrderDTO $orderDTO): OrderResponse
    {
        return new OrderResponse(
            id: $orderDTO->id,
            totalPrice: $orderDTO->totalPrice,
            status: $orderDTO->status->value,
            orderItems: array_map($this->orderItemTransformer->run(...), $orderDTO->orderItemDTOs),
        );
    }
}
