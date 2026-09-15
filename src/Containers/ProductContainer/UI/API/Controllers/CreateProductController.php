<?php

declare(strict_types=1);

namespace App\Containers\ProductContainer\UI\API\Controllers;

use App\Containers\ProductContainer\Actions\CreateProductAction;
use App\Containers\ProductContainer\UI\API\Requests\CreateProductRequest;
use App\Containers\ProductContainer\UI\API\Responses\ProductResponse;
use App\Containers\ProductContainer\UI\API\Transformers\ProductTransformer;
use App\Containers\ProductContainer\Values\ProductValue;
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
#[Route(path: '/api/v1/product', name: 'api_create_product', methods: ['POST'])]
final class CreateProductController extends ApiController
{
    public function __construct(
        private readonly CreateProductAction $action,
        private readonly ProductTransformer $transformer,
    ) {}

    /**
     * @throws Throwable
     */
    #[OA\Post(
        operationId: 'api_create_product',
        description: 'Створення нового продукту юзером',
        summary: 'Створення нового продукту',
    )]
    #[OA\Response(
        response: 200,
        description: 'Success',
        content: new OA\JsonContent(ref: new Model(type: ProductResponse::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
    )]
    #[OA\RequestBody(
        description: 'Request body',
        content: new OA\JsonContent(ref: new Model(type: CreateProductRequest::class)),
    )]
    public function __invoke(#[MapRequestPayload] CreateProductRequest $request): JsonResponse
    {
        $value = ProductValue::create(name: $request->name, description: $request->description, price: $request->price);

        $product = $this->action->run($value);

        return $this->json($this->transformer->transform($product));
    }
}
