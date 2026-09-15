<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Controllers;

use App\Containers\OrderContainer\Actions\GetOrdersAction;
use App\Containers\OrderContainer\UI\API\Responses\OrderResponse;
use App\Containers\OrderContainer\UI\API\Transformers\OrderTransformer;
use App\Ship\Attributes\RateLimiter;
use App\Ship\Interfaces\AuthUserInterface;
use App\Ship\Parents\Controllers\ApiController;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[OA\Tag('OrderController')]
#[RateLimiter(policy: 'jwt')]
#[Route('/api/v1/orders', name: 'api_list_orders', methods: ['GET'])]
final class GetOrdersController extends ApiController
{
    public function __construct(
        private readonly GetOrdersAction $action,
        private readonly OrderTransformer $transformer,
    ) {}

    #[OA\Get(
        operationId: 'api_list_orders',
        description: 'Отримання списку усіх замовлень для конкретного юзера',
        summary: 'Отримання списку замовлень',
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: OrderResponse::class),
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
        $orderDTOs = $this->action->run($user->getId());

        return $this->json(['data' => array_map($this->transformer->transform(...), $orderDTOs)]);
    }
}
