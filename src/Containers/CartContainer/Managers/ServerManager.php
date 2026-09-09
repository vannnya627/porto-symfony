<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Managers\Interfaces\CartServerManagerInterface;
use App\Containers\CartContainer\Tasks\FindCartWithItemsAndProductsTask;
use App\Containers\CartContainer\Tasks\SaveCartTask;
use App\Containers\UserContainer\Data\Entities\User;
use App\Ship\Parents\Managers\Manager;

final readonly class ServerManager extends Manager implements CartServerManagerInterface
{
    public function __construct(
        private FindCartWithItemsAndProductsTask $findCartWithItemsAndProductsTask,
        private SaveCartTask $saveCartTask,
    ) {}

    public function findCartWithItemsAndProducts(User $user): ?Cart
    {
        return $this->findCartWithItemsAndProductsTask->run($user);
    }

    public function saveCart(Cart $cart): void
    {
        $this->saveCartTask->run($cart);
    }
}
