<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\UI\API\Controllers;

use App\Containers\ProductContainer\Actions\GetProductAction;
use App\Containers\ProductContainer\UI\API\Responses\ProductResponse;
use App\Ship\Attributes\RateLimiter;
use App\Ship\DTO\ErrorResponseDTO;
use App\Ship\Parents\Controllers\ApiController;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag('ProductController')]
#[RateLimiter]
#[Route('/api/v1/product/{productId}', name: 'api_get_product', methods: ['GET'])]
final class GetProductController extends ApiController
{
    public function __construct(private readonly GetProductAction $action) {}

    #[OA\Get(
        operationId: 'api_get_product',
        description: 'Отримання продукт за id',
        summary: 'Отримання продукту',
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(ref: new Model(type: ProductResponse::class)),
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
    )]
    #[OA\Parameter(
        name: 'productId',
        description: 'Id Продукту',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
    )]
    public function __invoke(int $productId): JsonResponse
    {
        $result = $this->action->run($productId);

        $response = ProductResponse::create($result);

        return $this->json($response);
    }
}
