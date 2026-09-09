<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Actions;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Managers\UserClientManager;
use App\Containers\CartContainer\Tasks\FindCartWithItemsAndProductsTask;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Email;

final readonly class GetCartItemsAction extends Action
{
    public function __construct(
        private UserClientManager $userClientManager,
        private FindCartWithItemsAndProductsTask $findCartWithItemsAndProductsTask,
    ) {}

    public function run(Email $email): ?Cart
    {
        $user = $this->userClientManager->getUserByEmail($email);
        $cart = $this->findCartWithItemsAndProductsTask->run($user);

        if (null === $cart || $cart->cartItems->isEmpty()) {
            return null;
        }

        return $cart;
    }
}
