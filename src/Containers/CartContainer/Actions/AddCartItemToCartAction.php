<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Actions;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Managers\ProductClientManager;
use App\Containers\CartContainer\Tasks\FindCartByIdTask;
use App\Containers\CartContainer\Tasks\SaveAndCommitCartTask;
use App\Containers\CartContainer\Values\AddCartItemValue;
use App\Ship\Parents\Actions\Action;

final readonly class AddCartItemToCartAction extends Action
{
    public function __construct(
        private ProductClientManager $productClientManager,
        private FindCartByIdTask $findCartByIdTask,
        private SaveAndCommitCartTask $saveCartTask,
    ) {}

    public function run(AddCartItemValue $value): void
    {
        $userId = $value->userId;
        // Todo змінити на ProductDTO(+test)
        $product = $this->productClientManager->getProductById($value->productId);

        $cart = $this->findCartByIdTask->run($userId);

        $cart ??= Cart::create($userId);

        $cart->addItem($product->id, $value->quantity);

        $this->saveCartTask->run($cart);
    }
}
