<?php

declare(strict_types=1);

namespace App\Containers\CartContainer\UI\API\Controllers;

use App\Containers\CartContainer\Actions\GetCartItemsAction;
use App\Containers\CartContainer\UI\API\Transformers\CartItemTransformer;
use App\Containers\ProductContainer\UI\API\Responses\ProductResponse;
use App\Ship\Attributes\RateLimiter;
use App\Ship\Parents\Controllers\ApiController;
use App\Ship\ValueObjects\Email;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;

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
    public function __invoke(): JsonResponse
    {
        $emailStr = $this->getUser()?->getUserIdentifier();
        if (!$emailStr) {
            throw new UnauthorizedHttpException('Bearer', 'Користувач не авторизований');
        }

        $cart = $this->action->run(Email::create($emailStr));
        // TODO може бути 1+n перевірити
        $response = $cart ? array_map($this->transformer->run(...), $cart->cartItems->toArray()) : null;

        return $this->json(['data' => $response]);
    }
}
