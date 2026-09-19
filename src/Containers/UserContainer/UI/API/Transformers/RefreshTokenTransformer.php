<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Transformers;

use App\Containers\UserContainer\UI\API\Responses\RefreshTokenResponse;
use App\Containers\UserContainer\Values\RefreshTokenAndJwtTokenValue;
use App\Ship\Parents\Transformers\Transformer;

final readonly class RefreshTokenTransformer extends Transformer
{
    public function transform(RefreshTokenAndJwtTokenValue $value): RefreshTokenResponse
    {
        return RefreshTokenResponse::create($value);
    }
}
