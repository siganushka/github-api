<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\Configuration;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ConfigurationTest extends TestCase
{
    public function testAll(): void
    {
        $configuration = static::createConfiguration();
        static::assertSame('test_client_id', $configuration['client_id']);
        static::assertSame('test_client_secret', $configuration['client_secret']);
    }

    public function testClientIdMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_id" is missing');

        new Configuration(['client_secret' => 'test_client_secret']);
    }

    public function testClientSecretMissingOptionsException(): void
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "client_secret" is missing');

        new Configuration(['client_id' => 'test_client_id']);
    }

    public static function createConfiguration(): Configuration
    {
        $options = [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ];

        return new Configuration($options);
    }
}
