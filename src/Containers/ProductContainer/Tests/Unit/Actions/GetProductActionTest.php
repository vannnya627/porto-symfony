<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tests\Unit\Actions;

use App\Containers\ProductContainer\Actions\GetProductAction;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Exceptions\ProductNotFoundException;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Ship\ValueObjects\Price;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

#[AllowMockObjectsWithoutExpectations]
final class GetProductActionTest extends AbstractTestCase
{
    private GetProductByIdTask|MockObject $getProductByIdTask;
    private GetProductAction $action;

    protected function setUp(): void
    {
        $this->getProductByIdTask = $this->createMock(GetProductByIdTask::class);
        $this->action = new GetProductAction($this->getProductByIdTask);
    }

    /**
     * @throws ReflectionException
     */
    public function testRunReturnsProduct(): void
    {
        $productId = 1;
        $product = Product::create('Name', 'Desc', Price::create(100));
        $this->setEntityId($product, $productId);

        $this->getProductByIdTask->expects($this->once())
            ->method('run')
            ->with($productId)
            ->willReturn($product);

        $result = $this->action->run($productId);

        $this->assertSame($product, $result);
    }

    public function testRunThrowsProductNotFoundException(): void
    {
        $productId = 1;

        $this->getProductByIdTask->expects($this->once())
            ->method('run')
            ->with($productId)
            ->willThrowException(new ProductNotFoundException($productId));

        $this->expectException(ProductNotFoundException::class);
        $this->action->run($productId);
    }
}
