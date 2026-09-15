<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Actions;

use App\Containers\CartContainer\Managers\PublicValues\CartItemPublicValue;
use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\DTOs\OrderDTO;
use App\Containers\OrderContainer\DTOs\OrderItemDTO;
use App\Containers\OrderContainer\Events\OrderCreatedEvent;
use App\Containers\OrderContainer\Exceptions\EmptyCartException;
use App\Containers\OrderContainer\Managers\CartClientManager;
use App\Containers\OrderContainer\Managers\ProductClientManager;
use App\Containers\OrderContainer\Tasks\SaveAndCommitOrderTask;
use App\Ship\Parents\Actions\Action;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class CreateOrderAction extends Action
{
    public function __construct(
        private CartClientManager $cartClientManager,
        private ProductClientManager $productClientManager,
        private SaveAndCommitOrderTask $saveAndCommitOrderTask,
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function run(int $userId): OrderDTO
    {
        $cart = $this->cartClientManager->findCartWithItemsTask($userId);

        if (null === $cart || [] === $cart->items) {
            throw new EmptyCartException($cart?->id);
        }

        $order = Order::create($userId);

        $productIds = array_map(fn(CartItemPublicValue $cartItem) => $cartItem->productId, $cart->items);

        $productsDictionary = array_column($this->productClientManager->getProductsByIds($productIds), null, 'id');

        $orderItemDTOs = [];
        foreach ($cart->items as $cartItem) {
            $product = $productsDictionary[$cartItem->productId] ?? null;
            if (null !== $product) {
                $order->addItem($cartItem->productId, $product->name, $cartItem->quantity, $product->price);
                $orderItemDTOs[] = new OrderItemDTO(
                    productId: $product->id,
                    productName: $product->name,
                    quantity: $cartItem->quantity,
                    price: $product->price,
                );
            }
        }

        $this->saveAndCommitOrderTask->run($order);
        $this->eventDispatcher->dispatch(new OrderCreatedEvent($userId));

        return new OrderDTO(id: $order->id, totalPrice: $order->totalPrice, status: $order->status, orderItemDTOs: $orderItemDTOs);
    }
}
