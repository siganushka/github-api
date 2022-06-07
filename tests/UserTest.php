<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\User;
use Siganushka\ApiClient\RequestOptions;
use Siganushka\ApiClient\Response\ResponseFactory;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UserTest extends TestCase
{
    public function testResolve(): void
    {
        $request = static::createRequest();

        $resolved = $request->resolve(['access_token' => 'foo']);
        static::assertSame('foo', $resolved['access_token']);
        static::assertSame(['access_token'], $request->getResolver()->getDefinedOptions());
    }

    public function testSend(): void
    {
        $data = [
            'id' => 65535,
        ];

        $response = ResponseFactory::createMockResponseWithJson($data);

        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $request = static::createRequest();
        $request->setHttpClient($httpClient);

        $parsedResponse = $request->send(['access_token' => 'foo']);
        static::assertSame($data, $parsedResponse);
    }

    public function testConfigureRequest(): void
    {
        $request = static::createRequest();
        $requestOptions = new RequestOptions();

        $configureRequestRef = new \ReflectionMethod($request, 'configureRequest');
        $configureRequestRef->setAccessible(true);
        $configureRequestRef->invoke($request, $requestOptions, $request->resolve(['access_token' => 'foo']));

        static::assertSame('GET', $requestOptions->getMethod());
        static::assertSame(User::URL, $requestOptions->getUrl());
        static::assertSame([
            'headers' => [
                'Authorization' => 'token foo',
            ],
        ], $requestOptions->toArray());
    }

    public function testAccessTokenMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "access_token" is missing');

        $request = static::createRequest();
        $request->resolve();
    }

    public function testAccessTokenInvalidOptionsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "access_token" with value 123 is expected to be of type "string", but is of type "int"');

        $request = static::createRequest();
        $request->resolve(['access_token' => 123]);
    }

    public static function createRequest(): User
    {
        return new User();
    }
}
