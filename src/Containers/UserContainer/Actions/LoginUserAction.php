<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Actions;

use App\Containers\UserContainer\Events\UserLoggedInEvent;
use App\Containers\UserContainer\Exceptions\InvalidCredentialsException;
use App\Containers\UserContainer\Tasks\CheckUserCreditsTask;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\FindUserByEmailTask;
use App\Containers\UserContainer\Tasks\RefreshToken\CreateRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\SaveAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Values\LoginValue;
use App\Containers\UserContainer\Values\UserValue;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Email;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class LoginUserAction extends Action
{
    public function __construct(
        private FindUserByEmailTask $findUserByEmailTask,
        private CreateJWTTokenTask $createJWTTokenTask,
        private CheckUserCreditsTask $checkUserCredits,
        private CreateRefreshTokenTask $createRefreshTokenTask,
        private SaveAndCommitRefreshTokenTask $saveAndCommitRefreshTokenTask,
        #[Autowire(param: 'gesdinet_jwt_refresh_token.ttl')]
        private int $ttl,
        private MessageBusInterface $bus,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    public function run(UserValue $value): LoginValue
    {
        $email = Email::create($value->email);

        $user = $this->findUserByEmailTask->run($email);

        if (null === $user || false === $this->checkUserCredits->run($user, $value->password)) {
            throw new InvalidCredentialsException(['email' => $value->email, 'password' => $value->password]);
        }

        $token = $this->createJWTTokenTask->run($user);

        $refreshToken = $this->createRefreshTokenTask->run($user, $this->ttl);
        $this->saveAndCommitRefreshTokenTask->run($refreshToken);

        $this->bus->dispatch(new UserLoggedInEvent($email));

        return LoginValue::create(userId: $user->id, email: $user->email->value, token: $token, refreshToken: (string) $refreshToken->getRefreshToken());
    }
}
