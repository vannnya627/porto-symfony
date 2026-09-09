<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\Exceptions;

use App\Ship\Parents\Exceptions\Exception;

final class ProductNotFoundException extends Exception
{
    public function __construct(private readonly int $productId)
    {
        parent::__construct('Продукт не знайдено');
    }

    public function getContext(): array
    {
        return ['productId' => $this->productId];
    }
}
