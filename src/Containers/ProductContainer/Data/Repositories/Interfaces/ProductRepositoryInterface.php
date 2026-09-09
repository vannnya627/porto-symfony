<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Data\Repositories\Interfaces;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Exceptions\ProductNotFoundException;

interface ProductRepositoryInterface
{
    /**
     * @throws ProductNotFoundException
     */
    public function getById(int $productId): Product;

    public function saveAndCommit(Product $product): void;

    /**
     * @return list<Product>
     */
    public function findProducts(): array;

    public function removeAndCommit(Product $product): void;
}
