<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tests\Unit\Actions;

use App\Containers\ProductContainer\Actions\CreateProductAction;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Tasks\SaveAndCommitProductTask;
use App\Containers\ProductContainer\Values\ProductValue;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

#[AllowMockObjectsWithoutExpectations]
final class CreateProductActionTest extends AbstractTestCase
{
    private SaveAndCommitProductTask|MockObject $saveProductTask;
    private CreateProductAction $action;

    protected function setUp(): void
    {
        $this->saveProductTask = $this->createMock(SaveAndCommitProductTask::class);
        $this->action = new CreateProductAction($this->saveProductTask);
    }

    /**
     * @throws Throwable
     */
    public function testRunSuccessfullyCreatesProduct(): void
    {
        $value = ProductValue::create(name: 'Test Product', description: 'Desc', price: 150);

        $this->saveProductTask->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (Product $product) use ($value) {
                $this->assertEquals($value->name, $product->name);
                $this->assertEquals($value->description, $product->description);
                $this->assertEquals($value->price, $product->price->value);

                $this->setEntityId($product, 99);
            });

        $result = $this->action->run($value);

        $this->assertEquals(99, $result->id);
        $this->assertEquals('Test Product', $result->name);
    }
}
