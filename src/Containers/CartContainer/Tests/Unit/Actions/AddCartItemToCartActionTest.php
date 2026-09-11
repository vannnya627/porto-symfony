<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Tests\Unit\Actions;

use App\Containers\CartContainer\Actions\AddCartItemToCartAction;
use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Managers\ProductClientManager;
use App\Containers\CartContainer\Tasks\FindCartByIdTask;
use App\Containers\CartContainer\Tasks\SaveAndCommitCartTask;
use App\Containers\CartContainer\Values\AddCartItemValue;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Exceptions\ProductNotFoundException;
use App\Ship\ValueObjects\Price;
use App\Ship\ValueObjects\Quantity;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

#[AllowMockObjectsWithoutExpectations]
final class AddCartItemToCartActionTest extends AbstractTestCase
{
    private ProductClientManager|MockObject $productClientManager;
    private FindCartByIdTask|MockObject $findCartByIdTask;
    private SaveAndCommitCartTask|MockObject $saveCartTask;
    private AddCartItemToCartAction $action;

    protected function setUp(): void
    {
        $this->productClientManager = $this->createMock(ProductClientManager::class);
        $this->findCartByIdTask = $this->createMock(FindCartByIdTask::class);
        $this->saveCartTask = $this->createMock(SaveAndCommitCartTask::class);

        $this->action = new AddCartItemToCartAction(
            $this->productClientManager,
            $this->findCartByIdTask,
            $this->saveCartTask,
        );
    }

    /**
     * @throws Throwable
     */
    public function testRunAddsItemToExistingCart(): void
    {
        $userId = 1;
        $productId = 100;
        $quantity = Quantity::create(2);

        $value = AddCartItemValue::create(userId: $userId, productId: $productId, quantity: $quantity);
        $product = Product::create(name: 'Test Product', description: 'Desc', price: Price::create(100));
        $this->setEntityId($product, $productId);

        $this->productClientManager->expects($this->once())
            ->method('getProductById')
            ->with($productId)
            ->willReturn($product);

        $cart = Cart::create($userId);
        $this->findCartByIdTask->expects($this->once())
            ->method('run')
            ->with($userId)
            ->willReturn($cart);

        $this->saveCartTask->expects($this->once())
            ->method('run')
            ->with($cart);

        $this->action->run($value);

        $this->assertCount(1, $cart->cartItems);
        $addedItem = $cart->cartItems->first();
        $this->assertEquals(2, $addedItem->quantity->value);
        $this->assertEquals($productId, $addedItem->productId);
    }

    /**
     * @throws Throwable
     */
    public function testRunCreatesNewCartIfNoneExists(): void
    {
        $userId = 1;
        $productId = 100;
        $quantity = Quantity::create(1);

        $value = AddCartItemValue::create(userId: $userId, productId: $productId, quantity: $quantity);
        $product = Product::create(name: 'Test Product', description: 'Desc', price: Price::create(100));
        $this->setEntityId($product, $productId);

        $this->productClientManager->expects($this->once())
            ->method('getProductById')
            ->with($productId)
            ->willReturn($product);

        $this->findCartByIdTask->expects($this->once())
            ->method('run')
            ->with($userId)
            ->willReturn(null);

        $this->saveCartTask->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (Cart $cart) use ($userId, $productId) {
                $this->assertEquals($userId, $cart->userId);
                $this->assertCount(1, $cart->cartItems);
                $this->assertEquals($productId, $cart->cartItems->first()->productId);
            });

        $this->action->run($value);
    }

    /**
     * @throws Throwable
     */
    public function testRunThrowsProductNotFoundException(): void
    {
        $userId = 1;
        $productId = 100;
        $quantity = Quantity::create(1);

        $value = AddCartItemValue::create(userId: $userId, productId: $productId, quantity: $quantity);

        $this->productClientManager->expects($this->once())
            ->method('getProductById')
            ->with($productId)
            ->willThrowException(new ProductNotFoundException($productId));

        $this->findCartByIdTask->expects($this->never())->method('run');
        $this->saveCartTask->expects($this->never())->method('run');

        $this->expectException(ProductNotFoundException::class);
        $this->action->run($value);
    }
}
