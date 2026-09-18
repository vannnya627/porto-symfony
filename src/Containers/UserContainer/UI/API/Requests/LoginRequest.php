<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Requests;

use App\Ship\Parents\Requests\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class LoginRequest extends Request
{
    public function __construct(
        #[OA\Property(description: 'Пошта користувача', example: 'user@gmail.com')]
        #[Assert\NotBlank(message: 'Email не може бути порожнім.')]
        #[Assert\Email]
        public string $email,
        #[OA\Property(description: 'Пароль користувача', example: '1234567890')]
        #[Assert\NotBlank(message: 'Пароль не може бути порожнім.')]
        public string $password,
    ) {}
}
