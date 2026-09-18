<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tests\Unit\Actions;

use App\Containers\UserContainer\Actions\LoginUserAction;
use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Exceptions\InvalidCredentialsException;
use App\Containers\UserContainer\Tasks\CheckUserCreditsTask;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\FindUserByEmailTask;
use App\Containers\UserContainer\Values\LoginValue;
use App\Containers\UserContainer\Values\UserValue;
use App\Ship\ValueObjects\Email;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

#[AllowMockObjectsWithoutExpectations]
final class LoginUserActionTest extends AbstractTestCase
{
    private FindUserByEmailTask|MockObject $getUserByEmailTask;
    private CreateJWTTokenTask|MockObject $createJWTTokenTask;
    private CheckUserCreditsTask|MockObject $checkUserCredits;
    private LoginUserAction $action;

    protected function setUp(): void
    {
        $this->getUserByEmailTask = $this->createMock(FindUserByEmailTask::class);
        $this->createJWTTokenTask = $this->createMock(CreateJWTTokenTask::class);
        $this->checkUserCredits = $this->createMock(CheckUserCreditsTask::class);

        $this->action = new LoginUserAction(
            $this->getUserByEmailTask,
            $this->createJWTTokenTask,
            $this->checkUserCredits,
        );
    }

    /**
     * @throws Throwable
     */
    public function testRunSuccessfullyLoginUserAndReturnsLoginValue(): void
    {
        $emailString = 'test@test.com';
        $passwordString = '1234567890';
        $hashedPassword = 'hashed_password_string';
        $token = 'jwt_test_token';

        $value = UserValue::create(email: $emailString, password: $passwordString);
        $emailVo = Email::create($emailString);

        $user = User::createCustomer($emailVo, $hashedPassword);
        $this->setEntityId($user, 1);

        $this->getUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn($user);

        $this->checkUserCredits->expects($this->once())
            ->method('run')
            ->with($user, $passwordString)
            ->willReturn(true);


        $this->createJWTTokenTask->expects($this->once())
            ->method('run')
            ->with($this->callback(fn(User $createdUser) => $createdUser->email->value === $emailString))
            ->willReturn($token);

        $result = $this->action->run($value);

        $expectedResponse = LoginValue::create(userId: 1, email: $emailString, token: $token);
        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @throws Throwable
     */
    public function testRunThrowsUserNotFoundException(): void
    {
        $emailString = 'test@test.com';
        $passwordString = '1234567890';

        $value = UserValue::create(email: $emailString, password: $passwordString);
        $emailVo = Email::create($emailString);

        $this->getUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn(null);

        $this->checkUserCredits->expects($this->never())
            ->method('run');


        $this->createJWTTokenTask->expects($this->never())
            ->method('run');

        $this->expectException(InvalidCredentialsException::class);
        $this->action->run($value);
    }

    /**
     * @throws Throwable
     */
    public function testRunThrowsInvalidCredentialsException(): void
    {
        $emailString = 'test@test.com';
        $passwordString = '1234567890';
        $hashedPassword = 'hashed_password_string';

        $value = UserValue::create(email: $emailString, password: $passwordString);
        $emailVo = Email::create($emailString);

        $user = User::createCustomer($emailVo, $hashedPassword);
        $this->setEntityId($user, 1);

        $this->getUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn($user);

        $this->checkUserCredits->expects($this->once())
            ->method('run')
            ->with($user, $passwordString)
            ->willReturn(false);


        $this->createJWTTokenTask->expects($this->never())
            ->method('run');

        $this->expectException(InvalidCredentialsException::class);
        $this->action->run($value);
    }
}
