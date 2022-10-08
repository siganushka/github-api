<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiFactory\Github\Configuration;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class ConfigurationTest extends TestCase
{
    public function testAll(): void
    {
        $configuration = static::create();

        static::assertInstanceOf(\Countable::class, $configuration);
        static::assertInstanceOf(\IteratorAggregate::class, $configuration);
        static::assertInstanceOf(\ArrayAccess::class, $configuration);
        static::assertSame(2, $configuration->count());

        static::assertEquals([
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ], $configuration->toArray());
    }

    public function testClientIdInvalidOptionsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "client_id" with value 123 is expected to be of type "string", but is of type "int"');

        static::create([
            'client_id' => 123,
            'client_secret' => 'test_client_secret',
        ]);
    }

    public function testClientSecretInvalidOptionsException(): void
    {
        $this->expectException(InvalidOptionsException::class);
        $this->expectExceptionMessage('The option "client_secret" with value 123 is expected to be of type "string", but is of type "int"');

        static::create([
            'client_id' => 'test_client_id',
            'client_secret' => 123,
        ]);
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
