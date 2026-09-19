<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Controllers;

use App\Containers\UserContainer\Actions\RefreshTokenAction;
use App\Containers\UserContainer\UI\API\Requests\RefreshTokenRequest;
use App\Containers\UserContainer\UI\API\Responses\RefreshTokenResponse;
use App\Containers\UserContainer\UI\API\Transformers\RefreshTokenTransformer;
use App\Ship\Attributes\RateLimiter;
use App\Ship\Core\Abstracts\Http\Controller;
use App\Ship\DTO\ErrorResponseDTO;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[OA\Tag('AuthController')]
#[RateLimiter(policy: 'auth')]
#[Route('/api/v1/token/refresh', name: 'refresh_token', methods: ['POST'])]
final class RefreshTokenController extends Controller
{
    public function __construct(
        private readonly RefreshTokenAction $action,
        private readonly RefreshTokenTransformer $transformer,
    ) {}

    #[OA\Post(
        operationId: 'refresh_token',
        description: 'Оновлення токену',
        summary: 'Оновлення токену',
    )]
    #[OA\Response(
        response: 200,
        description: 'Refresh token success',
        content: new OA\JsonContent(ref: new Model(type: RefreshTokenResponse::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid refresh token',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
    )]
    #[OA\RequestBody(
        description: 'Request body',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: RefreshTokenRequest::class)),
    )]
    public function __invoke(#[MapRequestPayload] RefreshTokenRequest $request): JsonResponse
    {
        $value = $this->action->run($request->refreshToken);

        return $this->json($this->transformer->transform($value));
    }
}
