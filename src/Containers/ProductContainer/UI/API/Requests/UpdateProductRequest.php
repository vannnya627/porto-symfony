<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\UI\API\Requests;

use App\Ship\Parents\Requests\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateProductRequest extends Request
{
    public function __construct(
        #[OA\Property(description: 'Повна назва товару', example: 'Apple MacBook Pro 14')]
        #[Assert\Length(max: 255, maxMessage: 'Поле name не може бути більше ніж 255 символів')]
        public ?string $name = null,
        #[OA\Property(description: 'Повна назва товару', example: 'Apple MacBook Pro 14')]
        #[Assert\Length(max: 5000, maxMessage: 'Поле name не може бути більше ніж 5000 символів')]
        public ?string $description = null,
        #[OA\Property(description: 'Ціна в копійках (ціле число)', example: 199900)]
        #[Assert\Positive(message: "Поле 'price' не може бути менше нуля або дорівнювати нулю")]
        #[Assert\Type('int', message: 'Ціна має бути цілим числом (копійки)')]
        public ?int $price = null,
    ) {}
}
