<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Exception\ParseResponseException;
use Siganushka\ApiClient\Github\AccessToken;
use Siganushka\ApiClient\Response\ResponseFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class AccessTokenTest extends TestCase
{
    public function testResolve(): void
    {
        $request = static::createRequest();

        $resolved = $request->resolve(['code' => 'foo']);
        static::assertSame('foo', $resolved['code']);
        static::assertNull($resolved['redirect_uri']);
    }

    public function testBuild(): void
    {
        $request = static::createRequest();
        $requestOptions = $request->build(['code' => 'foo']);

        static::assertSame('POST', $requestOptions->getMethod());
        static::assertSame(AccessToken::URL, $requestOptions->getUrl());
        static::assertSame([
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => [
                'client_id' => 'test_client_id',
                'client_secret' => 'test_client_secret',
                'code' => 'foo',
            ],
        ], $requestOptions->toArray());

        $requestOptions = $request->build(['code' => 'foo', 'redirect_uri' => '/foo']);
        static::assertSame([
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => [
                'client_id' => 'test_client_id',
                'client_secret' => 'test_client_secret',
                'code' => 'foo',
                'redirect_uri' => '/foo',
            ],
        ], $requestOptions->toArray());
    }

    public function testSend(): void
    {
        $data = [
            'access_token' => 'foo',
            'scope' => 12,
            'token_type' => 'bar',
        ];

        $response = ResponseFactory::createMockResponseWithJson($data);
        $client = new MockHttpClient($response);

        $request = static::createRequest();
        $request->setHttpClient($client);

        $result = $request->send(['code' => 'foo']);
        static::assertSame($data, $result);
    }

    public function testParseResponseException(): void
    {
        $this->expectException(ParseResponseException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('test error (error)');

        $data = [
            'error' => 'error',
            'error_description' => 'test error',
        ];

        $response = ResponseFactory::createMockResponseWithJson($data);

        $request = static::createRequest();
        $parseResponseRef = new \ReflectionMethod($request, 'parseResponse');
        $parseResponseRef->setAccessible(true);
        $parseResponseRef->invoke($request, $response);
    }

    public function testCodeMissingException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "code" is missing');

        $request = static::createRequest();
        $request->resolve();
    }

    public function testCodeInvalidException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "code" with value 123 is expected to be of type "string", but is of type "int"');

        $request = static::createRequest();
        $request->resolve(['code' => 123]);
    }

    public static function createRequest(): AccessToken
    {
        $cachePool = new FilesystemAdapter();
        $cachePool->clear();

        $configuration = ConfigurationTest::createConfiguration();

        return new AccessToken($cachePool, $configuration);
    }
}
