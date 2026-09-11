<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Tests\Unit\Actions;

use App\Containers\OrderContainer\Actions\GetOrdersAction;
use App\Containers\OrderContainer\Data\Entities\Order;
use App\Containers\OrderContainer\DTOs\OrderDTO;
use App\Containers\OrderContainer\Tasks\FindOrdersByUserIdTask;
use App\Ship\ValueObjects\Price;
use App\Ship\ValueObjects\Quantity;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;

#[AllowMockObjectsWithoutExpectations]
final class GetOrdersActionTest extends AbstractTestCase
{
    private FindOrdersByUserIdTask|MockObject $findOrdersByUserIdTask;
    private GetOrdersAction $action;

    protected function setUp(): void
    {
        $this->findOrdersByUserIdTask = $this->createMock(FindOrdersByUserIdTask::class);
        $this->action = new GetOrdersAction($this->findOrdersByUserIdTask);
    }

    public function testRunReturnsListOfOrderDTOs(): void
    {
        $userId = 1;

        $order = Order::create($userId);
        $this->setEntityId($order, 10);

        $order->addItem(100, 'Test Product', Quantity::create(2), Price::create(150));

        $this->findOrdersByUserIdTask->expects($this->once())
            ->method('run')
            ->with($userId)
            ->willReturn([$order]);

        $result = $this->action->run($userId);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(OrderDTO::class, $result[0]);

        $orderDTO = $result[0];
        $this->assertEquals(10, $orderDTO->id);
        $this->assertEquals(300, $orderDTO->totalPrice);
        $this->assertEquals('NEW', $orderDTO->status->value);

        $this->assertCount(1, $orderDTO->orderItemDTOs);
        $this->assertEquals(100, $orderDTO->orderItemDTOs[0]->productId);
        $this->assertEquals('Test Product', $orderDTO->orderItemDTOs[0]->productName);
        $this->assertEquals(2, $orderDTO->orderItemDTOs[0]->quantity->value);
        $this->assertEquals(150, $orderDTO->orderItemDTOs[0]->price->value);
    }

    public function testRunReturnsEmptyArrayWhenNoOrdersFound(): void
    {
        $userId = 1;

        $this->findOrdersByUserIdTask->expects($this->once())
            ->method('run')
            ->with($userId)
            ->willReturn([]);

        $result = $this->action->run($userId);

        $this->assertEquals([], $result);
    }
}
