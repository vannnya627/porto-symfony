<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Managers\Interfaces;

use App\Containers\CartContainer\Managers\PublicValues\CartPublicValue;

interface CartServerManagerInterface
{
    public function findCartWithItemsTask(int $userId): ?CartPublicValue;
}
