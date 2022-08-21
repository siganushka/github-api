<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\ApiClient\Github\GithubExtension;
use Siganushka\ApiClient\OptionsExtensionInterface;
use Siganushka\ApiClient\RequestInterface;

class GithubExtensionTest extends TestCase
{
    public function testAll(): void
    {
        $configuration = ConfigurationTest::create();

        $extension = new GithubExtension($configuration);
        static::assertCount(2, $extension->loadRequests());
        static::assertCount(1, $extension->loadOptionsExtensions());

        foreach ($extension->loadRequests() as $request) {
            static::assertInstanceOf(RequestInterface::class, $request);
        }

        foreach ($extension->loadOptionsExtensions() as $extension) {
            static::assertInstanceOf(OptionsExtensionInterface::class, $extension);
        }
    }
}
