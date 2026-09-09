<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Controllers;

use App\Containers\UserContainer\Actions\SignUpUserAction;
use App\Containers\UserContainer\UI\API\Requests\SignUpUserRequest;
use App\Containers\UserContainer\UI\API\Responses\SignUpUserResponse;
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
#[Route('/api/v1/auth/signUp', name: 'signUp', methods: ['POST'])]
final class SignUpController extends ApiController
{
    public function __construct(private readonly SignUpUserAction $action) {}

    #[OA\Post(
        operationId: 'signUp',
        description: 'Реєстрація нового юзера',
        summary: 'Реєстрація',
    )]
    #[OA\Response(
        response: 200,
        description: 'Sign up success',
        content: new OA\JsonContent(ref: new Model(type: SignUpUserResponse::class)),
    )]
    #[OA\Response(
        response: 409,
        description: 'User already exists',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation error',
        content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class)),
    )]
    #[OA\RequestBody(
        description: 'Request body',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: SignUpUserRequest::class)),
    )]
    public function __invoke(#[MapRequestPayload] SignUpUserRequest $request): JsonResponse
    {
        $value = UserValue::create($request->email, $request->password);

        $result = $this->action->run($value);

        $response = SignUpUserResponse::create($result);

        return $this->json($response, 201);
    }
}
