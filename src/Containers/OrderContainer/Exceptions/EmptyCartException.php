<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Exceptions;

use App\Ship\Exceptions\Interfaces\ApiExceptionInterface;
use App\Ship\Parents\Exceptions\Exception;

final class EmptyCartException extends Exception implements ApiExceptionInterface
{
    public function __construct(private readonly ?int $cartId)
    {
        parent::__construct('Кошик порожній');
    }

    public function getContext(): array
    {
        return ['cartId' => $this->cartId];
    }
}
