<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Actions;

use App\Containers\CartContainer\Data\Entities\CartItem;
use App\Containers\CartContainer\DTOs\CartItemDTO;
use App\Containers\CartContainer\Managers\ProductClientManager;
use App\Containers\CartContainer\Tasks\FindCartWithItemsTask;
use App\Ship\Parents\Actions\Action;

final readonly class GetCartItemsAction extends Action
{
    public function __construct(
        private FindCartWithItemsTask $findCartWithItemsTask,
        private ProductClientManager $productClientManager,
    ) {}

    /**
     * @return list<CartItemDTO>
     */
    public function run(int $userId): array
    {
        $cart = $this->findCartWithItemsTask->run($userId);

        if (null === $cart || $cart->cartItems->isEmpty()) {
            return [];
        }

        $productIds = $cart->cartItems->map(fn(CartItem $cartItem) => $cartItem->productId)->toArray();

        $productsDictionary = array_column($this->productClientManager->getProductsByIds($productIds), null, 'id');

        $cartItemDTOs = [];
        foreach ($cart->cartItems as $cartItem) {
            $product = $productsDictionary[$cartItem->productId] ?? null;

            if (null !== $product) {
                $cartItemDTOs[] = new CartItemDTO(productId: $product->id, productName: $product->name, quantity: $cartItem->quantity);
            }
        }

        return $cartItemDTOs;
    }
}
