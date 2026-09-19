<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Actions;

use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Exceptions\UserAlreadyExistsException;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\ExistsUserByEmailTask;
use App\Containers\UserContainer\Tasks\HashPasswordTask;
use App\Containers\UserContainer\Tasks\RefreshToken\CreateRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\SaveAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Tasks\SaveAndCommitUserTask;
use App\Containers\UserContainer\Values\SignUpValue;
use App\Containers\UserContainer\Values\UserValue;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Email;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class SignUpUserAction extends Action
{
    public function __construct(
        private ExistsUserByEmailTask $existsUserByEmailTask,
        private HashPasswordTask $createHashPasswordTask,
        private SaveAndCommitUserTask $saveUserTask,
        private CreateJWTTokenTask $createJWTTokenTask,
        private CreateRefreshTokenTask $createRefreshTokenTask,
        private SaveAndCommitRefreshTokenTask $saveAndCommitRefreshTokenTask,
        #[Autowire(param: 'gesdinet_jwt_refresh_token.ttl')]
        private int $ttl,
    ) {}

    public function run(UserValue $value): SignUpValue
    {
        $email = Email::create($value->email);

        if ($this->existsUserByEmailTask->run($email)) {
            throw new UserAlreadyExistsException($email->value);
        }

        $passwordHash = $this->createHashPasswordTask->run($value->password);

        $user = User::createCustomer($email, $passwordHash);

        $this->saveUserTask->run($user);

        $token = $this->createJWTTokenTask->run($user);

        $refreshToken = $this->createRefreshTokenTask->run($user, $this->ttl);
        $this->saveAndCommitRefreshTokenTask->run($refreshToken);

        return SignUpValue::create(userId: $user->id, email: $user->email->value, token: $token, refreshToken: (string) $refreshToken->getRefreshToken());
    }
}
