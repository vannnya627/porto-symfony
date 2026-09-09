<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Actions;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Managers\ProductClientManager;
use App\Containers\CartContainer\Managers\UserClientManager;
use App\Containers\CartContainer\Tasks\FindCartByIdTask;
use App\Containers\CartContainer\Tasks\SaveAndCommitCartTask;
use App\Containers\CartContainer\Values\AddCartItemValue;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Quantity;

final readonly class AddCartItemToCartAction extends Action
{
    public function __construct(
        private ProductClientManager $productClientManager,
        private UserClientManager $userClientManager,
        private FindCartByIdTask $findCartByIdTask,
        private SaveAndCommitCartTask $saveCartTask,
    ) {}

    public function run(AddCartItemValue $value): void
    {
        $user = $this->userClientManager->getUserByEmail($value->userEmail);
        $product = $this->productClientManager->getProductById($value->productId);

        $cart = $this->findCartByIdTask->run($user->id);

        if (null === $cart) {
            $cart = Cart::create($user);
            $user->addCart($cart);
        }

        $quantity = Quantity::create($value->quantity);
        $cart->addItem($product, $quantity);

        $this->saveCartTask->run($cart);
    }
}
