<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Transformers;

use App\Containers\UserContainer\UI\API\Responses\SignUpUserResponse;
use App\Containers\UserContainer\Values\SignUpValue;
use App\Ship\Parents\Transformers\Transformer;

final readonly class UserTransformer extends Transformer
{
    public function transform(SignUpValue $value): SignUpUserResponse
    {
        return SignUpUserResponse::create($value);
    }
}
