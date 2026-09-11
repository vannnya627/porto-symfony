<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tests\Unit\Actions;

use App\Containers\ProductContainer\Actions\DeleteProductAction;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Exceptions\ProductNotFoundException;
use App\Containers\ProductContainer\Tasks\DeleteProductTask;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Ship\ValueObjects\Price;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

#[AllowMockObjectsWithoutExpectations]
final class DeleteProductActionTest extends AbstractTestCase
{
    private DeleteProductTask|MockObject $deleteProductTask;
    private GetProductByIdTask|MockObject $getProductByIdTask;
    private DeleteProductAction $action;

    protected function setUp(): void
    {
        $this->deleteProductTask = $this->createMock(DeleteProductTask::class);
        $this->getProductByIdTask = $this->createMock(GetProductByIdTask::class);

        $this->action = new DeleteProductAction(
            $this->deleteProductTask,
            $this->getProductByIdTask,
        );
    }

    /**
     * @throws ReflectionException
     */
    public function testRunSuccessfullyDeletesProduct(): void
    {
        $productId = 1;
        $product = Product::create('Name', 'Desc', Price::create(100));
        $this->setEntityId($product, $productId);

        $this->getProductByIdTask->expects($this->once())
            ->method('run')
            ->with($productId)
            ->willReturn($product);

        $this->deleteProductTask->expects($this->once())
            ->method('run')
            ->with($product);

        $this->action->run($productId);
    }

    public function testRunThrowsProductNotFoundException(): void
    {
        $productId = 1;

        $this->getProductByIdTask->expects($this->once())
            ->method('run')
            ->with($productId)
            ->willThrowException(new ProductNotFoundException($productId));

        $this->deleteProductTask->expects($this->never())->method('run');

        $this->expectException(ProductNotFoundException::class);
        $this->action->run($productId);
    }
}
