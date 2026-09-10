<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\DTOs;

use App\Containers\OrderContainer\Enum\OrderStatus;
use App\Ship\Parents\DTOs\DTO;

final readonly class OrderDTO extends DTO
{
    /**
     * @param list<OrderItemDTO> $orderItemDTOs
     */
    public function __construct(
        public int $id,
        public int $totalPrice,
        public OrderStatus $status,
        public array $orderItemDTOs,
    ) {}
}
