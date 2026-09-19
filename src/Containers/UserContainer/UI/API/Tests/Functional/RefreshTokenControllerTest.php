<?php

declare(strict_types=1);

namespace App\Containers\UserContainer\UI\API\Tests\Functional;

use App\Ship\Parents\Tests\AbstractWebTestCase;

class RefreshTokenControllerTest extends AbstractWebTestCase
{
    public function testRefreshTokenSuccess(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get('doctrine')->getManager();

        $this->createUser($em);

        $client->request(
            'POST',
            '/api/v1/auth/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['email' => 'test@test.com', 'password' => 'password']),
        );

        $loginResponse = json_decode($client->getResponse()->getContent(), true);
        $realRefreshToken = $loginResponse['refreshToken'];

        $client->request(
            'POST',
            '/api/v1/token/refresh',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['refreshToken' => $realRefreshToken]),
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $responseContent = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($responseContent);
        $this->assertArrayHasKey('token', $responseContent);
        $this->assertIsString($responseContent['token']);
        $this->assertArrayHasKey('refreshToken', $responseContent);
        $this->assertIsString($responseContent['refreshToken']);
    }

    public function testRefreshTokenInvalidFormat(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/token/refresh',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['refreshToken' => 12345]),
        );

        $this->assertResponseStatusCodeSame(422);
    }

    public function testRefreshTokenInvalidOrExpired(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/v1/token/refresh',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['refreshToken' => 'invalid_or_expired_token_string_here']),
        );

        $this->assertResponseStatusCodeSame(401);
    }
}
