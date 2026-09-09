<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers\Interfaces;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\UserContainer\Data\Entities\User;

interface CartServerManagerInterface
{
    public function findCartWithItemsAndProducts(User $user): ?Cart;

    public function saveCart(Cart $cart): void;
}
