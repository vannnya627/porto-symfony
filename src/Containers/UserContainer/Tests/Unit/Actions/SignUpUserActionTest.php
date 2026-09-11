<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tests\Unit\Actions;

use App\Containers\UserContainer\Actions\SignUpUserAction;
use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Exceptions\UserAlreadyExistsException;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\ExistsUserByEmailTask;
use App\Containers\UserContainer\Tasks\HashPasswordTask;
use App\Containers\UserContainer\Tasks\SaveAndCommitUserTask;
use App\Containers\UserContainer\Values\SignUpValue;
use App\Containers\UserContainer\Values\UserValue;
use App\Ship\ValueObjects\Email;
use App\Ship\Parents\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Throwable;

#[AllowMockObjectsWithoutExpectations]
final class SignUpUserActionTest extends AbstractTestCase
{
    private ExistsUserByEmailTask|MockObject $existsUserByEmailTask;
    private HashPasswordTask|MockObject $hashPasswordTask;
    private SaveAndCommitUserTask|MockObject $saveUserTask;
    private CreateJWTTokenTask|MockObject $createJWTTokenTask;
    private SignUpUserAction $action;

    protected function setUp(): void
    {
        $this->existsUserByEmailTask = $this->createMock(ExistsUserByEmailTask::class);
        $this->hashPasswordTask = $this->createMock(HashPasswordTask::class);
        $this->saveUserTask = $this->createMock(SaveAndCommitUserTask::class);
        $this->createJWTTokenTask = $this->createMock(CreateJWTTokenTask::class);

        $this->action = new SignUpUserAction(
            $this->existsUserByEmailTask,
            $this->hashPasswordTask,
            $this->saveUserTask,
            $this->createJWTTokenTask,
        );
    }

    /**
     * @throws Throwable
     */
    public function testRunSuccessfullyCreatesUserAndReturnsSignUpValue(): void
    {
        $emailString = 'test@test.com';
        $passwordString = '1234567890';
        $hashedPassword = 'hashed_password_string';
        $token = 'jwt_test_token';

        $value = UserValue::create(email: $emailString, password: $passwordString);
        $emailVo = Email::create($emailString);

        $this->existsUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn(false);

        $this->hashPasswordTask->expects($this->once())
            ->method('run')
            ->with($passwordString)
            ->willReturn($hashedPassword);

        $this->saveUserTask->expects($this->once())
            ->method('run')
            ->willReturnCallback(function (User $savedUser) use ($emailString) {
                $this->assertEquals($emailString, $savedUser->email->value);
                $this->setEntityId($savedUser, 1);
            });

        $this->createJWTTokenTask->expects($this->once())
            ->method('run')
            ->with($this->callback(fn(User $createdUser) => $createdUser->email->value === $emailString))
            ->willReturn($token);

        $result = $this->action->run($value);

        $expectedResponse = SignUpValue::create(userId: 1, email: $emailString, token: $token);
        $this->assertEquals($expectedResponse, $result);
    }

    /**
     * @throws Throwable
     */
    public function testRunThrowsUserAlreadyExistsException(): void
    {
        $emailString = 'test@test.com';
        $passwordString = '1234567890';
        $value = UserValue::create(email: $emailString, password: $passwordString);
        $emailVo = Email::create($emailString);

        $this->existsUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn(true);

        $this->hashPasswordTask->expects($this->never())->method('run');
        $this->saveUserTask->expects($this->never())->method('run');
        $this->createJWTTokenTask->expects($this->never())->method('run');

        $this->expectException(UserAlreadyExistsException::class);
        $this->action->run($value);
    }
}
