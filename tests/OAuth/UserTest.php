<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiFactory\Exception\ParseResponseException;
use Siganushka\ApiFactory\Github\OAuth\User;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class UserTest extends TestCase
{
    protected User $request;

    protected function setUp(): void
    {
        $this->request = new User();
    }

    public function testResolve(): void
    {
        static::assertEquals([
            'access_token' => 'foo',
        ], $this->request->resolve(['access_token' => 'foo']));
    }

    public function testBuild(): void
    {
        $requestOptions = $this->request->build(['access_token' => 'foo']);

        static::assertSame('GET', $requestOptions->getMethod());
        static::assertSame(User::URL, $requestOptions->getUrl());
        static::assertEquals([
            'headers' => [
                'Authorization' => 'token foo',
            ],
        ], $requestOptions->toArray());
    }

    public function testSend(): void
    {
        $data = ['id' => 65535];
        /** @var string */
        $body = json_encode($data);

        $mockResponse = new MockResponse($body);
        $client = new MockHttpClient($mockResponse);

        $result = (new User($client))->send(['access_token' => 'foo']);
        static::assertSame($data, $result);
    }

    public function testSendWithParseResponseException(): void
    {
        $this->expectException(ParseResponseException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('test error (error)');

        $data = ['error' => 'error', 'error_description' => 'test error'];
        /** @var string */
        $body = json_encode($data);

        $mockResponse = new MockResponse($body);
        $client = new MockHttpClient($mockResponse);

        (new User($client))->send(['access_token' => 'foo']);
    }

    public function testAccessTokenMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "access_token" is missing');

        $this->request->build();
    }

    public function testAccessTokenInvalidOptionsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "access_token" with value 123 is expected to be of type "string", but is of type "int"');

        $this->request->build(['access_token' => 123]);
    }
}
