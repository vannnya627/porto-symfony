<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\Tests\Unit\Actions;

use App\Containers\UserContainer\Actions\RefreshTokenAction;
use App\Containers\UserContainer\Data\Entities\User;
use App\Containers\UserContainer\Exceptions\InvalidRefreshTokenException;
use App\Containers\UserContainer\Tasks\CreateJWTTokenTask;
use App\Containers\UserContainer\Tasks\FindUserByEmailTask;
use App\Containers\UserContainer\Tasks\RefreshToken\CreateRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\DeleteAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\FindOldRefreshTokenTask;
use App\Containers\UserContainer\Tasks\RefreshToken\SaveAndCommitRefreshTokenTask;
use App\Containers\UserContainer\Values\RefreshTokenAndJwtTokenValue;
use App\Ship\Parents\Tests\AbstractTestCase;
use App\Ship\ValueObjects\Email;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;

#[AllowMockObjectsWithoutExpectations]
final class RefreshTokenActionTest extends AbstractTestCase
{
    private FindOldRefreshTokenTask|MockObject $findOldRefreshTokenTask;
    private FindUserByEmailTask|MockObject $findUserByEmailTask;
    private CreateJWTTokenTask|MockObject $createJWTTokenTask;
    private CreateRefreshTokenTask|MockObject $createRefreshTokenTask;
    private SaveAndCommitRefreshTokenTask|MockObject $saveAndCommitRefreshTokenTask;
    private DeleteAndCommitRefreshTokenTask|MockObject $deleteRefreshTokenTask;
    private int $ttl = 10;
    private RefreshTokenAction $action;

    protected function setUp(): void
    {
        $this->findOldRefreshTokenTask = $this->createMock(FindOldRefreshTokenTask::class);
        $this->findUserByEmailTask = $this->createMock(FindUserByEmailTask::class);
        $this->createJWTTokenTask = $this->createMock(CreateJWTTokenTask::class);
        $this->createRefreshTokenTask = $this->createMock(CreateRefreshTokenTask::class);
        $this->saveAndCommitRefreshTokenTask = $this->createMock(SaveAndCommitRefreshTokenTask::class);
        $this->deleteRefreshTokenTask = $this->createMock(DeleteAndCommitRefreshTokenTask::class);

        $this->action = new RefreshTokenAction(
            $this->findOldRefreshTokenTask,
            $this->findUserByEmailTask,
            $this->createRefreshTokenTask,
            $this->saveAndCommitRefreshTokenTask,
            $this->deleteRefreshTokenTask,
            $this->ttl,
            $this->createJWTTokenTask,
        );
    }

    public function testRunSuccessfullyRefreshTokenIsValid(): void
    {
        $emailString = 'test@test.com';
        $hashedPassword = 'hashed_password_string';
        $token = 'jwt_test_token';
        $refreshTokenStr = 'refresh_token_test';

        $emailVo = Email::create($emailString);

        $user = User::createCustomer($emailVo, $hashedPassword);
        $this->setEntityId($user, 1);

        $oldRefreshTokenMock = $this->createMock(RefreshTokenInterface::class);
        $oldRefreshTokenMock->method('getRefreshToken')->willReturn($refreshTokenStr);

        $this->findOldRefreshTokenTask->expects($this->once())
            ->method('run')
            ->with($refreshTokenStr)
            ->willReturn($oldRefreshTokenMock);

        $oldRefreshTokenMock->expects($this->once())
            ->method('isValid')
            ->willReturn(true);

        $oldRefreshTokenMock->expects($this->once())
            ->method('getUsername')
            ->willReturn($emailString);

        $this->findUserByEmailTask->expects($this->once())
            ->method('run')
            ->with($emailVo)
            ->willReturn($user);

        $this->createJWTTokenTask->expects($this->once())
            ->method('run')
            ->with($this->callback(fn(User $createdUser) => $createdUser->email->value === $emailString))
            ->willReturn($token);

        $newRefreshTokenMock = $this->createMock(RefreshTokenInterface::class);
        $newRefreshTokenMock->method('getRefreshToken')->willReturn($refreshTokenStr);

        $this->createRefreshTokenTask->expects($this->once())
            ->method('run')
            ->with($user, $this->ttl)
            ->willReturn($newRefreshTokenMock);

        $this->saveAndCommitRefreshTokenTask->expects($this->once())
            ->method('run')
            ->with($newRefreshTokenMock);

        $this->deleteRefreshTokenTask->expects($this->once())
            ->method('run')
            ->with($oldRefreshTokenMock);

        $result = $this->action->run($refreshTokenStr);

        $expectedResponse = RefreshTokenAndJwtTokenValue::create(token: $token, refreshToken: $refreshTokenStr);
        $this->assertEquals($expectedResponse, $result);
    }

    public function testRunThrowsInvalidRefreshTokenExceptionWhenIsNotValid(): void
    {
        $emailString = 'test@test.com';
        $hashedPassword = 'hashed_password_string';
        $refreshTokenStr = 'refresh_token_test';

        $emailVo = Email::create($emailString);

        $user = User::createCustomer($emailVo, $hashedPassword);
        $this->setEntityId($user, 1);

        $oldRefreshTokenMock = $this->createMock(RefreshTokenInterface::class);
        $oldRefreshTokenMock->method('getRefreshToken')->willReturn($refreshTokenStr);

        $this->findOldRefreshTokenTask->expects($this->once())
            ->method('run')
            ->with($refreshTokenStr)
            ->willReturn($oldRefreshTokenMock);

        $oldRefreshTokenMock->expects($this->once())
            ->method('isValid')
            ->willReturn(false);

        $oldRefreshTokenMock->expects($this->never())
            ->method('getUsername');

        $this->findUserByEmailTask->expects($this->never())
            ->method('run');

        $this->createJWTTokenTask->expects($this->never())
            ->method('run');

        $this->createRefreshTokenTask->expects($this->never())
            ->method('run');


        $this->saveAndCommitRefreshTokenTask->expects($this->never())
            ->method('run');

        $this->deleteRefreshTokenTask->expects($this->never())
            ->method('run');

        $this->expectException(InvalidRefreshTokenException::class);
        $this->action->run($refreshTokenStr);
    }
}
