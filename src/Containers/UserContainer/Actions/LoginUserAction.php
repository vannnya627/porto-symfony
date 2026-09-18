<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Actions;

use App\Containers\UserContainer\Exceptions\InvalidCredentialsException;
use App\Containers\UserContainer\Tasks\CheckUserCreditsTask;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\FindUserByEmailTask;
use App\Containers\UserContainer\Values\LoginValue;
use App\Containers\UserContainer\Values\UserValue;
use App\Ship\Parents\Actions\Action;
use App\Ship\ValueObjects\Email;

final readonly class LoginUserAction extends Action
{
    public function __construct(
        private FindUserByEmailTask $getUserByEmailTask,
        private CreateJWTTokenTask $createJWTTokenTask,
        private CheckUserCreditsTask $checkUserCredits,
    ) {}

    public function run(UserValue $value): LoginValue
    {
        $email = Email::create($value->email);

        $user = $this->getUserByEmailTask->run($email);

        if (null === $user || false === $this->checkUserCredits->run($user, $value->password)) {
            throw new InvalidCredentialsException(['email' => $value->email, 'password' => $value->password]);
        }

        $token = $this->createJWTTokenTask->run($user);

        return LoginValue::create(userId: $user->id, email: $user->email->value, token: $token);
    }
}
