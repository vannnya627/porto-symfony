<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Tests\Unit\Actions;

use App\Containers\ProductContainer\Actions\GetProductsAction;
use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Tasks\GetAllProductTask;
use App\Ship\ValueObjects\Price;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

#[AllowMockObjectsWithoutExpectations]
final class GetProductsActionTest extends AbstractTestCase
{
    private GetAllProductTask|MockObject $getAllProductTask;
    private GetProductsAction $action;

    protected function setUp(): void
    {
        $this->getAllProductTask = $this->createMock(GetAllProductTask::class);
        $this->action = new GetProductsAction($this->getAllProductTask);
    }

    /**
     * @throws ReflectionException
     */
    public function testRunReturnsListOfProducts(): void
    {
        $product1 = Product::create('Product 1', 'Desc', Price::create(100));
        $this->setEntityId($product1, 1);

        $product2 = Product::create('Product 2', 'Desc', Price::create(200));
        $this->setEntityId($product2, 2);

        $products = [$product1, $product2];

        $this->getAllProductTask->expects($this->once())
            ->method('run')
            ->willReturn($products);

        $result = $this->action->run();

        $this->assertSame($products, $result);
        $this->assertCount(2, $result);
    }

    public function testRunReturnsEmptyArrayWhenNoProductsExist(): void
    {
        $this->getAllProductTask->expects($this->once())
            ->method('run')
            ->willReturn([]);

        $result = $this->action->run();

        $this->assertEquals([], $result);
    }
}
