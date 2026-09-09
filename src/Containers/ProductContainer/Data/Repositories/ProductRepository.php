<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Data\Repositories;

use App\Containers\ProductContainer\Data\Entities\Product;
use App\Containers\ProductContainer\Data\Repositories\Interfaces\ProductRepositoryInterface;
use App\Containers\ProductContainer\Exceptions\ProductNotFoundException;
use App\Ship\Parents\Repositories\Repository;
use App\Ship\Traits\RepositorySupportTrait;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends Repository<Product>
 */
final class ProductRepository extends Repository implements ProductRepositoryInterface
{
    use RepositorySupportTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getById(int $productId): Product
    {
        return $this->find($productId) ?? throw new ProductNotFoundException($productId);
    }

    /**
     * @return list<Product>
     */
    public function findProducts(): array
    {
        return $this->findAll();
    }
}
