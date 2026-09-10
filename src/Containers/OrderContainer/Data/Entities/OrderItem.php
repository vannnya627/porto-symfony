<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Data\Entities;

use App\Ship\Parents\Entities\Entity;
use App\Ship\ValueObjects\Price;
use App\Ship\ValueObjects\Quantity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class OrderItem extends Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) Order $order;

    #[ORM\Column]
    public private(set) int $productId;

    #[ORM\Column]
    public private(set) string $productName;

    #[ORM\Embedded(class: Quantity::class, columnPrefix: false)]
    public private(set) Quantity $quantity;

    #[ORM\Embedded(class: Price::class, columnPrefix: false)]
    public private(set) Price $price;

    private function __construct() {}

    public static function create(Order $order, int $productId, string $productName, Quantity $quantity, Price $price): static
    {
        $orderItem = new self();
        $orderItem->order = $order;
        $orderItem->productId = $productId;
        $orderItem->productName = $productName;
        $orderItem->quantity = $quantity;
        $orderItem->price = $price;

        return $orderItem;
    }
}
