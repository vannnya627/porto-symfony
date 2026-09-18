<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Transformers;

use App\Containers\UserContainer\UI\API\Responses\LoginUserResponse;
use App\Containers\UserContainer\Values\LoginValue;
use App\Ship\Parents\Transformers\Transformer;

final readonly class LoginValueTransformer extends Transformer
{
    public function transform(LoginValue $value): LoginUserResponse
    {
        return LoginUserResponse::create($value);
    }
}
