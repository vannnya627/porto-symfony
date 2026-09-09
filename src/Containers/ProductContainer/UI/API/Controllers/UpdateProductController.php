<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\UI\API\Controllers;

use App\Containers\ProductContainer\Actions\UpdateProductAction;
use App\Containers\ProductContainer\UI\API\Requests\UpdateProductRequest;
use App\Containers\ProductContainer\UI\API\Responses\ProductResponse;
use App\Containers\ProductContainer\Values\UpdateProductValue;
use App\Ship\Attributes\RateLimiter;
use App\Ship\DTO\ErrorResponseDTO;
use App\Ship\Parents\Controllers\ApiController;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[OA\Tag('ProductController')]
#[RateLimiter]
#[Route(path: '/api/v1/product/{productId}', name: 'api_update_product', methods: ['PATCH'])]
final class UpdateProductController extends ApiController
{
    public function __construct(private readonly UpdateProductAction $action) {}

    /**
     * @throws Throwable
     */
    #[OA\Patch(
        operationId: 'api_update_product',
        description: 'Оновлення вибіркових полів продукту',
        summary: 'Оновлення продукту',
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
    #[OA\RequestBody(
        description: 'Request body',
        content: new OA\JsonContent(ref: new Model(type: UpdateProductRequest::class)),
    )]
    #[OA\Parameter(
        name: 'productId',
        description: 'Id Продукту',
        in: 'path',
        required: true,
        schema: new OA\Schema(type: 'integer'),
    )]
    public function __invoke(#[MapRequestPayload] UpdateProductRequest $request, int $productId): JsonResponse
    {
        $value = UpdateProductValue::create(name: $request->name, description: $request->description, price: $request->price);

        $response = ProductResponse::create($this->action->run(productId: $productId, value: $value));

        return $this->json($response);
    }
}
