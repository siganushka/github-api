<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiFactory\Github\ConfigurationExtension;
use Siganushka\ApiFactory\Github\OAuth\AccessToken;
use Siganushka\ApiFactory\Github\OAuth\Client;
use Siganushka\ApiFactory\ResolverExtensionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationExtensionTest extends TestCase
{
    protected ResolverExtensionInterface $extension;

    protected function setUp(): void
    {
        $this->extension = new ConfigurationExtension(ConfigurationTest::create());
    }

    public function testConfigureOptions(): void
    {
        $resolver = new OptionsResolver();
        $resolver->setDefined(['client_id', 'client_secret']);
        $this->extension->configureOptions($resolver);

        static::assertEquals([
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
        ], $resolver->resolve());
    }

    public function testGetExtendedClasses(): void
    {
        static::assertEquals([
            Client::class,
            AccessToken::class,
        ], ConfigurationExtension::getExtendedClasses());
    }
}
