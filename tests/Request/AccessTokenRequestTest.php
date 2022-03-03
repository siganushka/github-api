<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests\Request;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Exception\ParseResponseException;
use Siganushka\ApiClient\Github\Request\AccessTokenRequest;
use Siganushka\ApiClient\Github\Tests\ConfigurationTest;
use Siganushka\ApiClient\Response\ResponseFactory;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class AccessTokenRequestTest extends TestCase
{
    public function testAll(): void
    {
        $request = static::createRequest();
        static::assertNull($request->getMethod());
        static::assertNull($request->getUrl());
        static::assertSame([], $request->getOptions());

        $request->build(['code' => 'foo']);
        static::assertSame('POST', $request->getMethod());
        static::assertSame(AccessTokenRequest::URL, $request->getUrl());

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
        $options = [
            'access_token' => 'foo',
            'token_type' => 'bar',
            'scope' => '',
        ];

        /** @var string */
        $body = json_encode($options);
        $response = ResponseFactory::createMockResponse($body);

        $request = static::createRequest();
        static::assertSame($options, $request->parseResponse($response));
    }

    public function testParseResponseException(): void
    {
        $this->expectException(ParseResponseException::class);
        $this->expectExceptionMessage('bar (foo)');

        $options = [
            'error' => 'foo',
            'error_description' => 'bar',
        ];

        /** @var string */
        $body = json_encode($options);
        $response = ResponseFactory::createMockResponse($body);

        $request = static::createRequest();
        $request->parseResponse($response);
    }

    public static function createRequest(): AccessTokenRequest
    {
        $configuration = ConfigurationTest::createConfiguration();
        $request = new AccessTokenRequest($configuration);

        return $request;
    }
}
