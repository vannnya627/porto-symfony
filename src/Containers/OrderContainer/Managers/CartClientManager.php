<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Managers;

use App\Containers\CartContainer\Managers\Interfaces\CartServerManagerInterface;
use App\Containers\CartContainer\Managers\PublicValues\CartPublicValue;
use App\Ship\Parents\Managers\Manager;

readonly class CartClientManager extends Manager
{
    public function __construct(private CartServerManagerInterface $cartServerManager) {}

    public function findCartWithItemsTask(int $userId): ?CartPublicValue
    {
        return $this->cartServerManager->findCartWithItemsTask($userId);
    }
}
