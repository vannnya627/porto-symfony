<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Requests;

use App\Ship\Parents\Requests\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class RefreshTokenRequest extends Request
{
    public function __construct(
        #[OA\Property(description: 'JWT токен')]
        #[Assert\NotBlank(message: 'Refresh токен не може бути порожнім')]
        #[Assert\Length(min: 32, max: 255, minMessage: 'Некоректна довжина токена', maxMessage: 'Токен занадто довгий')]
        public string $refreshToken,
    ) {}
}
