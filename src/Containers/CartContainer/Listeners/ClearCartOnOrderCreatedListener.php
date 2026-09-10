<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Listeners;

use App\Containers\CartContainer\Tasks\FindCartByIdTask;
use App\Containers\CartContainer\Tasks\SaveAndCommitCartTask;
use App\Containers\OrderContainer\Events\OrderCreatedEvent;
use App\Ship\Parents\Listeners\Listener;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: OrderCreatedEvent::class)]
final readonly class ClearCartOnOrderCreatedListener extends Listener
{
    public function __construct(
        private FindCartByIdTask $findCartByIdTask,
        private SaveAndCommitCartTask $saveAndCommitCartTask,
    ) {}

    public function __invoke(OrderCreatedEvent $event): void
    {
        $cart = $this->findCartByIdTask->run($event->userId);

        if (null !== $cart) {
            $cart->clear();
            $this->saveAndCommitCartTask->run($cart);
        }
    }
}
