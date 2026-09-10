<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Actions;

use App\Containers\OrderContainer\DTOs\OrderDTO;
use App\Containers\OrderContainer\DTOs\OrderItemDTO;
use App\Containers\OrderContainer\Tasks\FindOrdersByUserIdTask;
use App\Ship\Parents\Actions\Action;

final readonly class GetOrdersAction extends Action
{
    public function __construct(
        private FindOrdersByUserIdTask $findOrdersByUserIdTask,
    ) {}

    /**
     * @return list<OrderDTO>
     */
    public function run(int $userId): array
    {
        $orders = $this->findOrdersByUserIdTask->run($userId);

        $orderDTOs = [];
        foreach ($orders as $order) {
            $orderItemDTOs = [];

            foreach ($order->orderItems as $item) {
                $orderItemDTOs[] = new OrderItemDTO(
                    productId: $item->productId,
                    productName: $item->productName,
                    quantity: $item->quantity,
                    price: $item->price,
                );
            }

            $orderDTOs[] = new OrderDTO(
                id: $order->id,
                totalPrice: $order->totalPrice,
                status: $order->status,
                orderItemDTOs: $orderItemDTOs,
            );
        }

        return $orderDTOs;
    }
}
