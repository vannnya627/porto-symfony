<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Actions;

use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\Exceptions\EmptyCartException;
use App\Containers\OrderContainer\Managers\CartClientManager;
use App\Containers\OrderContainer\Managers\UserClientManager;
use App\Containers\OrderContainer\Tasks\CommitOrderTask;
use App\Containers\OrderContainer\Tasks\SaveOrderTask;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Email;

final readonly class CreateOrderAction extends Action
{
    public function __construct(
        private UserClientManager $userClientManager,
        private CartClientManager $cartClientManager,
        private SaveOrderTask $saveOrderTask,
        private CommitOrderTask $commitOrderTask,
    ) {}

    public function run(Email $email): Order
    {
        $user = $this->userClientManager->getUserByEmail($email);
        $cart = $this->cartClientManager->findCartWithItemsAndProducts($user);

        if (null === $cart || $cart->cartItems->isEmpty()) {
            throw new EmptyCartException($cart?->id);
        }
        $order = Order::create($user);

        foreach ($cart->cartItems as $cartItem) {
            $order->addItem($cartItem->product, $cartItem->quantity);
        }

        $cart->clear();

        $this->saveOrderTask->run($order);
        $this->cartClientManager->saveCart($cart);

        $this->commitOrderTask->run();

        return $order;
    }
}
