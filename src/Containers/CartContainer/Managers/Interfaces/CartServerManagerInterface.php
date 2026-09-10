<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers\Interfaces;

use App\Containers\CartContainer\Data\Entities\Cart;

interface CartServerManagerInterface
{
    public function findCartWithItemsTask(int $userId): ?Cart;

    public function saveCart(Cart $cart): void;
}
