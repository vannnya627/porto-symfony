<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Data\Entities;

use App\Ship\Parents\Entities\Entity;
use App\Ship\ValueObjects\Quantity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
final class CartItem extends Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\ManyToOne(targetEntity: Cart::class, inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    public private(set) Cart $cart;

    #[ORM\Column]
    public private(set) int $productId;

    #[ORM\Embedded(class: Quantity::class, columnPrefix: false)]
    public private(set) Quantity $quantity;

    private function __construct() {}

    public function addQuantity(Quantity $newQuantity): void
    {
        $this->quantity = $this->quantity->add($newQuantity);
    }

    public static function create(Cart $cart, int $productId, Quantity $quantity): static
    {
        $cartItem = new self();
        $cartItem->cart = $cart;
        $cartItem->productId = $productId;
        $cartItem->quantity = $quantity;

        return $cartItem;
    }
}
