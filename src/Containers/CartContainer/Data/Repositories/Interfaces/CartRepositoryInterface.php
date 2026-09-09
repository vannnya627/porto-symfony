<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Data\Repositories\Interfaces;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\UserContainer\Data\Entities\User;

interface CartRepositoryInterface
{
    public function findByUserId(int $userId): ?Cart;

    public function saveAndCommit(Cart $cart): void;

    public function findCartWithItemsAndProducts(User $user): ?Cart;

    public function save(Cart $cart): void;
}
