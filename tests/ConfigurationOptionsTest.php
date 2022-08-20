<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\AccessToken;
use Siganushka\ApiClient\Github\Configuration;
use Siganushka\ApiClient\Github\ConfigurationOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationOptionsTest extends TestCase
{
    public function testConfigure(): void
    {
        $resolver = new OptionsResolver();

        $configurationOptions = static::create();
        $configurationOptions->configure($resolver);

        static::assertSame([
            'client_id',
            'client_secret',
        ], $resolver->getDefinedOptions());

        static::assertSame([
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ], $resolver->resolve());

        static::assertSame([
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ], $resolver->resolve([
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ]));
    }

    public function testGetExtendedRequests(): void
    {
        $configurationOptions = static::create();

        static::assertSame([
            AccessToken::class,
        ], $configurationOptions::getExtendedRequests());
    }

    public static function create(Configuration $configuration = null): ConfigurationOptions
    {
        if (null === $configuration) {
            $configuration = ConfigurationTest::create();
        }

        return new ConfigurationOptions($configuration);
    }
}