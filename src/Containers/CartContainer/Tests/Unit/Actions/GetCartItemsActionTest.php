<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\Tests\Unit\Actions;

use App\Containers\CartContainer\Actions\GetCartItemsAction;
use App\Containers\CartContainer\Data\Entities\Cart;
use App\Containers\CartContainer\DTOs\CartItemDTO;
use App\Containers\CartContainer\Managers\ProductClientManager;
use App\Containers\CartContainer\Tasks\FindCartWithItemsTask;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Ship\ValueObjects\Price;
use App\Ship\ValueObjects\Quantity;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

#[AllowMockObjectsWithoutExpectations]
final class GetCartItemsActionTest extends AbstractTestCase
{
    private FindCartWithItemsTask|MockObject $findCartWithItemsTask;
    private ProductClientManager|MockObject $productClientManager;
    private GetCartItemsAction $action;

    protected function setUp(): void
    {
        $this->findCartWithItemsTask = $this->createMock(FindCartWithItemsTask::class);
        $this->productClientManager = $this->createMock(ProductClientManager::class);

        $this->action = new GetCartItemsAction(
            $this->findCartWithItemsTask,
            $this->productClientManager,
        );
    }

    public function testRunReturnsEmptyArrayWhenCartIsNull(): void
    {
        $userId = 1;

        $this->findCartWithItemsTask->expects($this->once())
            ->method('run')
            ->with($userId)
            ->willReturn(null);

        $this->productClientManager->expects($this->never())->method('getProductsByIds');

        $result = $this->action->run($userId);

        $this->assertEquals([], $result);
    }

    public function testRunReturnsEmptyArrayWhenCartIsEmpty(): void
    {
        $userId = 1;
        $cart = Cart::create($userId);

        $this->findCartWithItemsTask->expects($this->once())
            ->method('run')
            ->with($userId)
            ->willReturn($cart);

        $this->productClientManager->expects($this->never())->method('getProductsByIds');

        $result = $this->action->run($userId);

        $this->assertEquals([], $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testRunReturnsCartItemDTOs(): void
    {
        $userId = 1;
        $cart = Cart::create($userId);
        $cart->addItem(100, Quantity::create(2));
        $cart->addItem(101, Quantity::create(3));

        $this->findCartWithItemsTask->expects($this->once())
            ->method('run')
            ->with($userId)
            ->willReturn($cart);

        $product1 = Product::create(name: 'Product A', description: 'Desc', price: Price::create(100));
        $this->setEntityId($product1, 100);
        $product2 = Product::create(name: 'Product B', description: 'Desc', price: Price::create(200));
        $this->setEntityId($product2, 101);

        $this->productClientManager->expects($this->once())
            ->method('getProductsByIds')
            ->with([100, 101])
            ->willReturn([$product1, $product2]);

        $result = $this->action->run($userId);

        $expected = [
            new CartItemDTO(productId: 100, productName: 'Product A', quantity: Quantity::create(2)),
            new CartItemDTO(productId: 101, productName: 'Product B', quantity: Quantity::create(3)),
        ];

        $this->assertEquals($expected, $result);
    }
}
