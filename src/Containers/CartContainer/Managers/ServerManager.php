<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers;

use App\Containers\CartContainer\Managers\Interfaces\CartServerManagerInterface;
use App\Containers\CartContainer\Managers\PublicValues\CartItemPublicValue;
use App\Containers\CartContainer\Managers\PublicValues\CartPublicValue;
use App\Containers\CartContainer\Tasks\FindCartWithItemsTask;
use App\Ship\Parents\Managers\Manager;

final readonly class ServerManager extends Manager implements CartServerManagerInterface
{
    public function __construct(
        private FindCartWithItemsTask $findCartWithItemsTask,
    ) {}

    public function findCartWithItemsTask(int $userId): ?CartPublicValue
    {
        $cart = $this->findCartWithItemsTask->run($userId);

        if (null === $cart) {
            return null;
        }

        $cartItemPublicValue = array_values(array_map(CartItemPublicValue::create(...), $cart->cartItems->toArray()));

        return CartPublicValue::create($cart->id, $cartItemPublicValue);
    }
}
