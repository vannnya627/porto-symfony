<?php

declare(strict_types=1);

namespace App\Ship\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final readonly class RateLimiter
{
    public function __construct(
        public private(set) string $policy = 'non_jwt',
    ) {}
}
