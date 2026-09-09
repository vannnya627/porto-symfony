<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\UI\API\Controllers;

use App\Containers\OrderContainer\Actions\CreateOrderAction;
use App\Containers\OrderContainer\UI\API\Responses\OrderResponse;
use App\Containers\OrderContainer\UI\API\Transformers\OrderTransformer;
use App\Ship\Attributes\RateLimiter;
use App\Ship\DTO\ErrorResponseDTO;
use App\Ship\Parents\Controllers\ApiController;
use App\Ship\ValueObjects\Email;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[OA\Tag('OrderController')]
#[RateLimiter(policy: 'jwt')]
#[Route(path: '/api/v1/order', name: 'api_create_order', methods: ['POST'])]
final class CreateOrderController extends ApiController
{
    public function __construct(
        private readonly CreateOrderAction $action,
        private readonly OrderTransformer $transformer,
    ) {}

    /**
     * @throws Throwable
     */
    #[OA\Post(
        operationId: 'api_create_order',
        description: 'Створення замовлення на основі товарі у кошику певного користувача',
        summary: 'Створення замовлення',
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(ref: new Model(type: OrderResponse::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Cart Is Empty',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
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

        $order = $this->action->run(Email::create($emailStr));

        return $this->json($this->transformer->run($order));
    }
}
