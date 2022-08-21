<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests\OAuth;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\OAuth\Client;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientTest extends TestCase
{
    private ?Client $client = null;

    protected function setUp(): void
    {
        $this->client = new Client();
    }

    protected function tearDown(): void
    {
        $this->client = null;
    }

    public function testConfigure(): void
    {
        $resolver = new OptionsResolver();
        $this->client->configure($resolver);

        static::assertSame([
            'client_id',
            'client_secret',
            'redirect_uri',
            'login',
            'scope',
            'state',
            'allow_signup',
        ], $resolver->getDefinedOptions());

        static::assertSame([
            'redirect_uri' => null,
            'login' => null,
            'scope' => null,
            'state' => null,
            'allow_signup' => null,
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ], $resolver->resolve([
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ]));

        static::assertSame([
            'redirect_uri' => 'test_redirect_uri',
            'login' => 'test_login',
            'scope' => 'test_scope',
            'state' => 'test_state',
            'allow_signup' => 'true',
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ], $resolver->resolve([
            'client_id' => 'foo',
            'client_secret' => 'bar',
            'redirect_uri' => 'test_redirect_uri',
            'login' => 'test_login',
            'scope' => 'test_scope',
            'state' => 'test_state',
            'allow_signup' => 'true',
        ]));
    }

    public function testGetRedirectUrl(): void
    {
        $redirectUrl = $this->client->getRedirectUrl([
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ]);

        static::assertStringStartsWith(Client::URL, $redirectUrl);
        static::assertStringContainsString('client_id=foo', $redirectUrl);
        static::assertStringNotContainsString('redirect_uri=', $redirectUrl);
        static::assertStringNotContainsString('login=', $redirectUrl);
        static::assertStringNotContainsString('scope=', $redirectUrl);
        static::assertStringNotContainsString('state=', $redirectUrl);
        static::assertStringNotContainsString('allow_signup=', $redirectUrl);

        $redirectUrl = $this->client->getRedirectUrl([
            'client_id' => 'foo',
            'client_secret' => 'bar',
            'redirect_uri' => 'test_redirect_uri',
            'login' => 'test_login',
            'scope' => 'test_scope',
            'state' => 'test_state',
            'allow_signup' => 'true',
        ]);

        static::assertStringStartsWith(Client::URL, $redirectUrl);
        static::assertStringContainsString('client_id=foo', $redirectUrl);
        static::assertStringContainsString('redirect_uri=', $redirectUrl);
        static::assertStringContainsString('login=', $redirectUrl);
        static::assertStringContainsString('scope=', $redirectUrl);
        static::assertStringContainsString('state=', $redirectUrl);
        static::assertStringContainsString('allow_signup=', $redirectUrl);
    }

    public function testClientIdMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_id" is missing');

        $this->client->getRedirectUrl(['client_secret' => 'bar']);
    }

    public function testClientSecretMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_secret" is missing');

        $this->client->getRedirectUrl(['client_id' => 'foo']);
    }
}
