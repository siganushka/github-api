<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Psr\Cache\CacheItemPoolInterface;
use Siganushka\ApiClient\ExtensionInterface;
use Siganushka\ApiClient\Github\OAuth\AccessToken;
use Siganushka\ApiClient\Github\OAuth\User;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class GithubExtension implements ExtensionInterface
{
    private Configuration $configuration;
    private CacheItemPoolInterface $cachePool;

    public function __construct(Configuration $configuration, CacheItemPoolInterface $cachePool = null)
    {
        $this->configuration = $configuration;
        $this->cachePool = $cachePool ?? new FilesystemAdapter();
    }

    public function loadRequests(): array
    {
        return [
            new AccessToken($this->cachePool),
            new User(),
        ];
    }

    public function loadOptionsExtensions(): array
    {
        return [
            new ConfigurationOptions($this->configuration),
        ];
    }
}
