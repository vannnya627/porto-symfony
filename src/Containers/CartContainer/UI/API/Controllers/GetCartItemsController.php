<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\UI\API\Controllers;

use App\Containers\CartContainer\Actions\GetCartItemsAction;
use App\Containers\CartContainer\UI\API\Transformers\CartItemTransformer;
use App\Containers\ProductContainer\UI\API\Responses\ProductResponse;
use App\Ship\Attributes\RateLimiter;
use App\Ship\Interfaces\AuthUserInterface;
use App\Ship\Parents\Controllers\ApiController;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[OA\Tag('CartController')]
#[RateLimiter(policy: 'jwt')]
#[Route('/api/v1/cart', name: 'api_list_items', methods: ['GET'])]
final class GetCartItemsController extends ApiController
{
    public function __construct(
        private readonly GetCartItemsAction $action,
        private readonly CartItemTransformer $transformer,
    ) {}

    #[OA\Get(
        operationId: 'api_list_items',
        description: 'Отримання списку товарів у кошику для конкретного юзера',
        summary: 'Отримання списку товарів',
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: ProductResponse::class),
            ),
        ),
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
    public function __invoke(#[CurrentUser] AuthUserInterface $user): JsonResponse
    {
        $cartItemsDTos = $this->action->run($user->getId());

        $response = array_map($this->transformer->run(...), $cartItemsDTos);

        return $this->json(['data' => $response]);
    }
}
