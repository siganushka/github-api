<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use Siganushka\ApiClient\Exception\ParseResponseException;
use Siganushka\ApiClient\Github\User;
use Siganushka\ApiClient\Response\ResponseFactory;
use Siganushka\ApiClient\Test\RequestTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserTest extends RequestTestCase
{
    public function testConfigure(): void
    {
        $resolver = new OptionsResolver();
        $this->request->configure($resolver);

        static::assertSame([
            'access_token',
        ], $resolver->getDefinedOptions());

        static::assertSame([
            'access_token' => 'foo',
        ], $resolver->resolve(['access_token' => 'foo']));
    }

    public function testBuild(): void
    {
        $requestOptions = $this->request->build(['access_token' => 'foo']);

        static::assertSame('GET', $requestOptions->getMethod());
        static::assertSame(User::URL, $requestOptions->getUrl());
        static::assertSame([
            'headers' => [
                'Authorization' => 'token foo',
            ],
        ], $requestOptions->toArray());
    }

    public function testSend(): void
    {
        $data = [
            'id' => 65535,
        ];

        $response = ResponseFactory::createMockResponseWithJson($data);
        $client = new MockHttpClient($response);

        $result = $this->request->send($client, ['access_token' => 'foo']);
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

    protected function createRequest(): User
    {
        return new User();
    }
}
