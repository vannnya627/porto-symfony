<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\UI\API\Controllers;

use App\Containers\CartContainer\Actions\AddCartItemToCartAction;
use App\Containers\CartContainer\UI\API\Requests\AddItemRequest;
use App\Containers\CartContainer\Values\AddCartItemValue;
use App\Ship\Attributes\RateLimiter;
use App\Ship\DTO\ErrorResponseDTO;
use App\Ship\Interfaces\AuthUserInterface;
use App\Ship\Parents\Controllers\ApiController;
use App\Ship\ValueObjects\Quantity;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[OA\Tag('CartController')]
#[RateLimiter(policy: 'jwt')]
#[Route(path: '/api/v1/cart', name: 'api_add_product_to_cart', methods: ['POST'])]
final class AddItemToCartController extends ApiController
{
    public function __construct(private readonly AddCartItemToCartAction $action) {}

    #[OA\Post(
        operationId: 'api_add_product_to_cart',
        description: 'Додавання товару до кошика користувача',
        summary: 'Створення нового продукту',
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string'),
            ],
        ),
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
    )]
    #[OA\RequestBody(
        description: 'Request body',
        content: new OA\JsonContent(ref: new Model(type: AddItemRequest::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'JWT Exception',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'code', type: 'int'),
                new OA\Property(property: 'message', type: 'string'),
            ],
        ),
    )]
    public function __invoke(#[MapRequestPayload] AddItemRequest $request, #[CurrentUser] AuthUserInterface $user): JsonResponse
    {
        $this->action->run(
            AddCartItemValue::create(
                userId: $user->getId(),
                productId: $request->productId,
                quantity: Quantity::create($request->quantity),
            ),
        );

        return $this->json(['message' => 'item added success']);
    }
}
