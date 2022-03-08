<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Exception\ParseResponseException;
use Siganushka\ApiClient\Github\AccessToken;
use Siganushka\ApiClient\Response\ResponseFactory;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class AccessTokenTest extends TestCase
{
    public function testAll(): void
    {
        $request = static::createRequest();
        static::assertNull($request->getMethod());
        static::assertNull($request->getUrl());
        static::assertSame([], $request->getOptions());

        $request->build(['code' => 'foo']);
        static::assertSame('POST', $request->getMethod());
        static::assertSame(AccessToken::URL, $request->getUrl());

        /**
         * @var array{
         *  headers: array{ Accept: string },
         *  body: array{ code: string, client_id: string, client_secret: string }
         * }
         */
        $options = $request->getOptions();
        static::assertSame('application/json', $options['headers']['Accept']);
        static::assertSame('foo', $options['body']['code']);
        static::assertArrayNotHasKey('redirect_uri', $options['body']);

        $configuration = ConfigurationTest::createConfiguration();
        static::assertSame($configuration['client_id'], $options['body']['client_id']);
        static::assertSame($configuration['client_secret'], $options['body']['client_secret']);
    }

    public function testWithOptions(): void
    {
        $request = static::createRequest();
        $request->build(['code' => 'foo', 'redirect_uri' => 'http://localhost/foo.html']);

        /**
         * @var array{
         *  body: array{ redirect_uri: string }
         * }
         */
        $options = $request->getOptions();
        static::assertSame('http://localhost/foo.html', $options['body']['redirect_uri']);
    }

    public function testCodeMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "code" is missing');

        $request = static::createRequest();
        $request->build();
    }

    public function testParseResponse(): void
    {
        $data = [
            'access_token' => 'foo',
            'token_type' => 'bar',
            'scope' => '',
        ];

        /** @var string */
        $body = json_encode($data);
        $response = ResponseFactory::createMockResponse($body);

        $request = static::createRequest();
        static::assertSame($data, $request->parseResponse($response));
    }

    public function testParseResponseException(): void
    {
        $this->expectException(ParseResponseException::class);
        $this->expectExceptionMessage('bar (foo)');

        $data = [
            'error' => 'foo',
            'error_description' => 'bar',
        ];

        /** @var string */
        $body = json_encode($data);
        $response = ResponseFactory::createMockResponse($body);

        $request = static::createRequest();
        $request->parseResponse($response);
    }

    public static function createRequest(): AccessToken
    {
        $configuration = ConfigurationTest::createConfiguration();
        $request = new AccessToken($configuration);

        return $request;
    }
}
