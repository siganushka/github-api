<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\Configuration;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationTest extends TestCase
{
    public function testConfigure(): void
    {
        $resolver = new OptionsResolver();

        $configuration = static::create();
        $configuration->configure($resolver);

        static::assertSame([
            'client_id',
            'client_secret',
        ], $resolver->getDefinedOptions());

        static::assertSame([
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ], $resolver->resolve([
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ]));
    }

    public function testAll(): void
    {
        $configuration = static::create();

        static::assertInstanceOf(\Countable::class, $configuration);
        static::assertInstanceOf(\IteratorAggregate::class, $configuration);
        static::assertInstanceOf(\ArrayAccess::class, $configuration);
        static::assertSame(2, $configuration->count());

        static::assertSame([
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ], $configuration->toArray());
    }

    public function testClientIdMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_id" is missing');

        static::create(['client_secret' => 'bar']);
    }

    public function testClientIdInvalidOptionsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "client_id" with value 123 is expected to be of type "string", but is of type "int"');

        static::create(['client_id' => 123, 'client_secret' => 'bar']);
    }

    public function testClientSecretMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_secret" is missing');

        static::create(['client_id' => 'foo']);
    }

    public function testClientSecretInvalidOptionsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "client_secret" with value 123 is expected to be of type "string", but is of type "int"');

        static::create(['client_id' => 'foo', 'client_secret' => 123]);
    }

    public static function create(array $configs = null): Configuration
    {
        if (null === $configs) {
            $configs = [
                'client_id' => 'test_client_id',
                'client_secret' => 'test_client_secret',
            ];
        }

        return new Configuration($configs);
    }
}
