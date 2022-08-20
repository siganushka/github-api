<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use Siganushka\ApiClient\Exception\ParseResponseException;
use Siganushka\ApiClient\Github\AccessToken;
use Siganushka\ApiClient\Response\ResponseFactory;
use Siganushka\ApiClient\Test\RequestTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccessTokenTest extends RequestTestCase
{
    public function testConfigure(): void
    {
        $resolver = new OptionsResolver();
        $this->request->configure($resolver);

        static::assertSame([
            'client_id',
            'client_secret',
            'code',
            'redirect_uri',
        ], $resolver->getDefinedOptions());

        static::assertSame([
            'redirect_uri' => null,
            'client_id' => 'foo',
            'client_secret' => 'bar',
            'code' => 'baz',
        ], $resolver->resolve(['client_id' => 'foo', 'client_secret' => 'bar', 'code' => 'baz']));
    }

    public function testBuild(): void
    {
        $requestOptions = $this->request->build(['client_id' => 'foo', 'client_secret' => 'bar', 'code' => 'baz']);

        static::assertSame('POST', $requestOptions->getMethod());
        static::assertSame(AccessToken::URL, $requestOptions->getUrl());
        static::assertSame([
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => [
                'client_id' => 'foo',
                'client_secret' => 'bar',
                'code' => 'baz',
            ],
        ], $requestOptions->toArray());

        $requestOptions = $this->request->build(['client_id' => 'foo', 'client_secret' => 'bar', 'code' => 'baz', 'redirect_uri' => '/foo']);
        static::assertSame([
            'headers' => [
                'Accept' => 'application/json',
            ],
            'body' => [
                'client_id' => 'foo',
                'client_secret' => 'bar',
                'code' => 'baz',
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

        $result = $this->request->send($client, ['client_id' => 'foo', 'client_secret' => 'bar', 'code' => 'baz']);
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

        $parseResponseRef = new \ReflectionMethod($this->request, 'parseResponse');
        $parseResponseRef->setAccessible(true);
        $parseResponseRef->invoke($this->request, $response);
    }

    public function testClientIdMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_id" is missing');

        $this->request->build(['client_secret' => 'bar', 'code' => 'baz']);
    }

    public function testClientSecretMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_secret" is missing');

        $this->request->build(['client_id' => 'foo', 'code' => 'baz']);
    }

    public function testCodeMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "code" is missing');

        $this->request->build(['client_id' => 'foo', 'client_secret' => 'bar']);
    }

    protected function createRequest(): AccessToken
    {
        return new AccessToken();
    }
}
