<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests\Request;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\Request\UserRequest;
use Siganushka\ApiClient\Response\ResponseFactory;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class UserRequestTest extends TestCase
{
    public function testAll(): void
    {
        $request = static::createRequest();
        static::assertNull($request->getMethod());
        static::assertNull($request->getUrl());
        static::assertSame([], $request->getOptions());

        $request->build(['access_token' => 'foo']);
        static::assertSame('GET', $request->getMethod());
        static::assertSame(UserRequest::URL, $request->getUrl());

        /**
         * @var array{
         *  headers: array{ Authorization: string }
         * }
         */
        $options = $request->getOptions();
        static::assertSame('token foo', $options['headers']['Authorization']);
    }

    public function testAccessTokenMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "access_token" is missing');

        $request = static::createRequest();
        $request->build();
    }

    public function testParseResponse(): void
    {
        $options = [
            'login' => 'foo',
        ];

        /** @var string */
        $body = json_encode($options);
        $response = ResponseFactory::createMockResponse($body);

        $request = static::createRequest();
        static::assertSame($options, $request->parseResponse($response));
    }

    public static function createRequest(): UserRequest
    {
        return new UserRequest();
    }
}
