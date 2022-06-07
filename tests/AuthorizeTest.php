<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\Authorize;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;

class AuthorizeTest extends TestCase
{
    public function testAll(): void
    {
        $options = [
            'redirect_uri' => 'http://localhost',
            'login' => 'foo',
            'scope' => 'bar',
            'state' => 'baz',
            'allow_signup' => 'true',
        ];

        $authorize = static::createAuthorize();
        static::assertSame([], $authorize->resolve([]));
        static::assertSame($options, $authorize->resolve($options));
    }

    public function testInvalidOptionsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "allow_signup" with value false is invalid. Accepted values are: "true", "false"');

        $authorize = static::createAuthorize();
        $authorize->resolve(['allow_signup' => false]);
    }

    public function testUndefinedOptionsException(): void
    {
        $this->expectException(UndefinedOptionsException::class);
        $this->expectExceptionMessage('The option "baz" does not exist. Defined options are: "allow_signup", "login", "redirect_uri", "scope", "state"');

        $authorize = static::createAuthorize();
        $authorize->resolve(['baz' => 'test']);
    }

    public static function createAuthorize(): Authorize
    {
        $configuration = ConfigurationTest::createConfiguration();
        $authorize = new Authorize($configuration);

        return $authorize;
    }
}
