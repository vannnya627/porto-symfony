<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Controllers;

use App\Containers\UserContainer\Actions\LoginUserAction;
use App\Containers\UserContainer\UI\API\Requests\LoginRequest;
use App\Containers\UserContainer\UI\API\Responses\LoginUserResponse;
use App\Containers\UserContainer\UI\API\Transformers\LoginValueTransformer;
use App\Containers\UserContainer\Values\UserValue;
use App\Ship\Attributes\RateLimiter;
use App\Ship\DTO\ErrorResponseDTO;
use App\Ship\Parents\Controllers\ApiController;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag('AuthController')]
#[RateLimiter(policy: 'auth')]
#[Route('/api/v1/auth/login', name: 'login', methods: ['POST'])]
final class LoginController extends ApiController
{
    public function __construct(
        private readonly LoginUserAction $action,
        private readonly LoginValueTransformer $transformer,
    ) {}

    #[OA\Post(
        operationId: 'login',
        description: 'Вхід користувача',
        summary: 'Вхід',
    )]
    #[OA\Response(
        response: 200,
        description: 'Login success',
        content: new OA\JsonContent(ref: new Model(type: LoginUserResponse::class)),
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
    )]
    #[OA\RequestBody(
        description: 'Request body',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: LoginRequest::class)),
    )]
    public function __invoke(#[MapRequestPayload] LoginRequest $request): JsonResponse
    {
        $value = $this->action->run(UserValue::create($request->email, $request->password));

        return $this->json($this->transformer->transform($value));
    }
}
