<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\UI\API\Controllers;

use App\Containers\ProductContainer\Actions\GetProductsAction;
use App\Containers\ProductContainer\UI\API\Responses\ProductResponse;
use App\Containers\ProductContainer\UI\API\Transformers\ProductTransformer;
use App\Ship\Attributes\RateLimiter;
use App\Ship\Parents\Controllers\ApiController;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag('ProductController')]
#[RateLimiter]
#[Route('/api/v1/products', name: 'api_get_all_product', methods: ['GET'])]
final class GetProductsController extends ApiController
{
    public function __construct(
        private readonly GetProductsAction $action,
        private readonly ProductTransformer $transformer,
    ) {}

    #[OA\Get(
        operationId: 'api_get_all_product',
        description: 'Отримання всіх продукт без пагінації',
        summary: 'Отримання всіх продуктів',
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
    public function __invoke(): JsonResponse
    {
        $products = $this->action->run();

        return $this->json(['data' => array_map(callback: $this->transformer->run(...), array: $products)]);
    }
}
