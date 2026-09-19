<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Actions;

use App\Containers\UserContainer\Exceptions\InvalidRefreshTokenException;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\FindUserByEmailTask;
use App\Containers\UserContainer\Tasks\RefreshToken\CreateRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\DeleteAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\FindOldRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\SaveAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Values\RefreshTokenAndJwtTokenValue;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Email;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class RefreshTokenAction extends Action
{
    public function __construct(
        private FindOldRefreshTokenTask $findOldRefreshTokenTask,
        private FindUserByEmailTask $findUserByEmailTask,
        private CreateRefreshTokenTask $createRefreshTokenTask,
        private SaveAndCommitRefreshTokenTask $saveAndCommitRefreshTokenTask,
        private DeleteAndCommitRefreshTokenTask $deleteRefreshTokenTask,
        #[Autowire(param: 'gesdinet_jwt_refresh_token.ttl')]
        private int $ttl,
        private CreateJWTTokenTask $createJWTTokenTask,
    ) {}

    public function run(string $refreshToken): RefreshTokenAndJwtTokenValue
    {
        $oldRefreshToken = $this->findOldRefreshTokenTask->run($refreshToken) ?? throw new InvalidRefreshTokenException($refreshToken);

        if (false === $oldRefreshToken->isValid()) {
            throw new InvalidRefreshTokenException($refreshToken);
        }

        $username = $oldRefreshToken->getUsername() ?? throw new InvalidRefreshTokenException($refreshToken);

        $user = $this->findUserByEmailTask->run(Email::create($username)) ?? throw new InvalidRefreshTokenException($refreshToken);

        $token = $this->createJWTTokenTask->run($user);

        $newRefreshToken = $this->createRefreshTokenTask->run($user, $this->ttl);
        $this->saveAndCommitRefreshTokenTask->run($newRefreshToken);
        $this->deleteRefreshTokenTask->run($oldRefreshToken);

        return RefreshTokenAndJwtTokenValue::create(token: $token, refreshToken: (string) $newRefreshToken->getRefreshToken());
    }
}
