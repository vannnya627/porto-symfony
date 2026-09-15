<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Tests\Unit\Actions;

use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\Data\Entities\CartItem;
use App\Containers\CartContainer\Managers\PublicValues\CartItemPublicValue;
use App\Containers\CartContainer\Managers\PublicValues\CartPublicValue;
use App\Containers\OrderContainer\Actions\CreateOrderAction;
use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\Events\OrderCreatedEvent;
use App\Containers\OrderContainer\Exceptions\EmptyCartException;
use App\Containers\OrderContainer\Managers\CartClientManager;
use App\Containers\OrderContainer\Managers\ProductClientManager;
use App\Containers\OrderContainer\Tasks\SaveAndCommitOrderTask;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Ship\ValueObjects\Price;
use App\Ship\ValueObjects\Quantity;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AllowMockObjectsWithoutExpectations]
final class CreateOrderActionTest extends AbstractTestCase
{
    private CartClientManager|MockObject $cartClientManager;
    private ProductClientManager|MockObject $productClientManager;
    private SaveAndCommitOrderTask|MockObject $saveAndCommitOrderTask;
    private EventDispatcherInterface|MockObject $eventDispatcher;
    private CreateOrderAction $action;

    protected function setUp(): void
    {
        $this->cartClientManager = $this->createMock(CartClientManager::class);
        $this->productClientManager = $this->createMock(ProductClientManager::class);
        $this->saveAndCommitOrderTask = $this->createMock(SaveAndCommitOrderTask::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->action = new CreateOrderAction(
            $this->cartClientManager,
            $this->productClientManager,
            $this->saveAndCommitOrderTask,
            $this->eventDispatcher,
        );
    }

    /**
     * @throws Throwable
     */
    public function testRunCreatesOrderSuccessfully(): void
    {
        $userId = 1;
        $cart = Cart::create($userId);
        $this->setEntityId($cart, 10);
        $cart->addItem(100, Quantity::create(2));

        $cartItem = CartItem::create($cart, 100, Quantity::create(2));
        $cartPublicValue = CartPublicValue::create(10, [CartItemPublicValue::create($cartItem)]);

        $product = Product::create(name: 'Test Product', description: 'Desc', price: Price::create(150));
        $this->setEntityId($product, 100);

        $this->cartClientManager->expects($this->once())
            ->method('findCartWithItemsTask')
            ->with($userId)
            ->willReturn($cartPublicValue);

        $this->productClientManager->expects($this->once())
            ->method('getProductsByIds')
            ->with([100])
            ->willReturn([$product]);

        $this->saveAndCommitOrderTask->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (Order $order) use ($userId) {
                $this->assertEquals($userId, $order->userId);
                $this->assertEquals(300, $order->totalPrice); // 2 шт * 150
                $this->assertCount(1, $order->orderItems);

                $this->setEntityId($order, 99);
            });

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(OrderCreatedEvent::class));

        $result = $this->action->run($userId);

        $this->assertEquals(99, $result->id);
        $this->assertEquals(300, $result->totalPrice);
        $this->assertEquals('NEW', $result->status->value);
        $this->assertCount(1, $result->orderItemDTOs);
        $this->assertEquals(100, $result->orderItemDTOs[0]->productId);
        $this->assertEquals('Test Product', $result->orderItemDTOs[0]->productName);
    }

    /**
     * @throws Throwable
     */
    public function testRunThrowsEmptyCartExceptionWhenCartIsNull(): void
    {
        $userId = 1;

        $this->cartClientManager->expects($this->once())
            ->method('findCartWithItemsTask')
            ->with($userId)
            ->willReturn(null);

        $this->productClientManager->expects($this->never())->method('getProductsByIds');
        $this->saveAndCommitOrderTask->expects($this->never())->method('run');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->expectException(EmptyCartException::class);
        $this->action->run($userId);
    }

    /**
     * @throws Throwable
     */
    public function testRunThrowsEmptyCartExceptionWhenCartIsEmpty(): void
    {
        $userId = 1;
        $cart = Cart::create($userId);
        $this->setEntityId($cart, 1);

        $cartPublicValue = CartPublicValue::create(10, []);

        $this->cartClientManager->expects($this->once())
            ->method('findCartWithItemsTask')
            ->with($userId)
            ->willReturn($cartPublicValue);

        $this->productClientManager->expects($this->never())->method('getProductsByIds');
        $this->saveAndCommitOrderTask->expects($this->never())->method('run');
        $this->eventDispatcher->expects($this->never())->method('dispatch');

        $this->expectException(EmptyCartException::class);
        $this->action->run($userId);
    }
}
