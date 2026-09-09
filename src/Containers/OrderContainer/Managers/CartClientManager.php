<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Managers;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Managers\Interfaces\CartServerManagerInterface;
use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Managers\Manager;

final readonly class CartClientManager extends Manager
{
    public function __construct(private CartServerManagerInterface $cartServerManager) {}

    public function findCartWithItemsAndProducts(User $user): ?Cart
    {
        return $this->cartServerManager->findCartWithItemsAndProducts($user);
    }

    public function saveCart(Cart $cart): void
    {
        $this->cartServerManager->saveCart($cart);
    }
}
