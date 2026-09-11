<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tests\Unit\Actions;

use App\Containers\ProductContainer\Actions\UpdateProductAction;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Exceptions\ProductNotFoundException;
use App\Containers\ProductContainer\Tasks\GetProductByIdTask;
use App\Containers\ProductContainer\Tasks\SaveAndCommitProductTask;
use App\Containers\ProductContainer\Values\UpdateProductValue;
use App\Ship\ValueObjects\Price;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

#[AllowMockObjectsWithoutExpectations]
final class UpdateProductActionTest extends AbstractTestCase
{
    private GetProductByIdTask|MockObject $getProductByIdTask;
    private SaveAndCommitProductTask|MockObject $saveProductTask;
    private UpdateProductAction $action;

    protected function setUp(): void
    {
        $this->getProductByIdTask = $this->createMock(GetProductByIdTask::class);
        $this->saveProductTask = $this->createMock(SaveAndCommitProductTask::class);

        $this->action = new UpdateProductAction(
            $this->getProductByIdTask,
            $this->saveProductTask,
        );
    }

    /**
     * @throws Throwable
     */
    public function testRunSuccessfullyUpdatesProduct(): void
    {
        $productId = 1;
        $product = Product::create('Old Name', 'Old Desc', Price::create(100));
        $this->setEntityId($product, $productId);

        $value = UpdateProductValue::create(name: 'New Name', description: 'New Desc', price: 999);

        $this->getProductByIdTask->expects($this->once())
            ->method('run')
            ->with($productId)
            ->willReturn($product);

        $this->saveProductTask->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (Product $savedProduct) {
                $this->assertEquals('New Name', $savedProduct->name);
                $this->assertEquals('New Desc', $savedProduct->description);
                $this->assertEquals(999, $savedProduct->price->value);
            });

        $result = $this->action->run($productId, $value);

        $this->assertSame($product, $result);
        $this->assertEquals('New Name', $product->name);
        $this->assertEquals(999, $product->price->value);
    }

    /**
     * @throws Throwable
     */
    public function testRunThrowsProductNotFoundException(): void
    {
        $productId = 1;
        $value = UpdateProductValue::create(name: 'New Name', description: null, price: null);
        $this->getProductByIdTask->expects($this->once())
            ->method('run')
            ->with($productId)
            ->willThrowException(new ProductNotFoundException($productId));

        $this->saveProductTask->expects($this->never())->method('run');

        $this->expectException(ProductNotFoundException::class);
        $this->action->run($productId, $value);
    }
}
