<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tests\Unit\Actions;

use App\Containers\UserContainer\Actions\LoginUserAction;
use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Events\UserLoggedInEvent;
use App\Containers\UserContainer\Exceptions\InvalidCredentialsException;
use App\Containers\UserContainer\Tasks\CheckUserCreditsTask;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\FindUserByEmailTask;
use App\Containers\UserContainer\Tasks\RefreshToken\CreateRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\SaveAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Values\LoginValue;
use App\Containers\UserContainer\Values\UserValue;
use App\Ship\ValueObjects\Email;
use App\Ship\Parents\Tests\AbstractTestCase;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

#[AllowMockObjectsWithoutExpectations]
final class LoginUserActionTest extends AbstractTestCase
{
    private FindUserByEmailTask|MockObject $findUserByEmailTask;
    private CreateJWTTokenTask|MockObject $createJWTTokenTask;
    private CheckUserCreditsTask|MockObject $checkUserCredits;
    private CreateRefreshTokenTask|MockObject $createRefreshTokenTask;
    private SaveAndCommitRefreshTokenTask|MockObject $saveAndCommitRefreshTokenTask;
    private int $ttl = 10;
    private MessageBusInterface|MockObject $bus;
    private LoginUserAction $action;

    protected function setUp(): void
    {
        $this->findUserByEmailTask = $this->createMock(FindUserByEmailTask::class);
        $this->createJWTTokenTask = $this->createMock(CreateJWTTokenTask::class);
        $this->checkUserCredits = $this->createMock(CheckUserCreditsTask::class);
        $this->createRefreshTokenTask = $this->createMock(CreateRefreshTokenTask::class);
        $this->saveAndCommitRefreshTokenTask = $this->createMock(SaveAndCommitRefreshTokenTask::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->action = new LoginUserAction(
            $this->findUserByEmailTask,
            $this->createJWTTokenTask,
            $this->checkUserCredits,
            $this->createRefreshTokenTask,
            $this->saveAndCommitRefreshTokenTask,
            $this->ttl,
            $this->bus,
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
        $refreshTokenStr = 'refresh_token_test';

        $value = UserValue::create(email: $emailString, password: $passwordString);
        $emailVo = Email::create($emailString);

        $user = User::createCustomer($emailVo, $hashedPassword);
        $this->setEntityId($user, 1);

        $refreshTokenMock = $this->createMock(RefreshTokenInterface::class);
        $refreshTokenMock->method('getRefreshToken')->willReturn($refreshTokenStr);

        $this->findUserByEmailTask->expects($this->once())
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

        $this->createRefreshTokenTask->expects($this->once())
            ->method('run')
            ->with($user, $this->ttl)
            ->willReturn($refreshTokenMock);

        $this->saveAndCommitRefreshTokenTask->expects($this->once())
            ->method('run')
            ->with($refreshTokenMock);

        $this->bus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(UserLoggedInEvent::class))
            ->willReturnCallback(fn($event) => new Envelope($event));

        $result = $this->action->run($value);

        $expectedResponse = LoginValue::create(userId: 1, email: $emailString, token: $token, refreshToken: $refreshTokenStr);
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

        $this->findUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn(null);

        $this->checkUserCredits->expects($this->never())
            ->method('run');


        $this->createJWTTokenTask->expects($this->never())
            ->method('run');

        $this->createRefreshTokenTask->expects($this->never())
            ->method('run');

        $this->saveAndCommitRefreshTokenTask->expects($this->never())
            ->method('run');

        $this->bus->expects($this->never())
            ->method('dispatch');

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

        $this->findUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn($user);

        $this->checkUserCredits->expects($this->once())
            ->method('run')
            ->with($user, $passwordString)
            ->willReturn(false);

        $this->createRefreshTokenTask->expects($this->never())
            ->method('run');

        $this->saveAndCommitRefreshTokenTask->expects($this->never())
            ->method('run');

        $this->bus->expects($this->never())
            ->method('dispatch');

        $this->expectException(InvalidCredentialsException::class);
        $this->action->run($value);
    }
}
