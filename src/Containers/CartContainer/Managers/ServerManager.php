<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Managers\Interfaces\CartServerManagerInterface;
use App\Containers\CartContainer\Tasks\FindCartWithItemsTask;
use App\Containers\CartContainer\Tasks\SaveCartTask;
use App\Ship\Parents\Managers\Manager;

final readonly class ServerManager extends Manager implements CartServerManagerInterface
{
    public function __construct(
        private FindCartWithItemsTask $findCartWithItemsTask,
        private SaveCartTask $saveCartTask,
    ) {}

    public function findCartWithItemsTask(int $userId): ?Cart
    {
        return $this->findCartWithItemsTask->run($userId);
    }

    public function saveCart(Cart $cart): void
    {
        $this->saveCartTask->run($cart);
    }
}
