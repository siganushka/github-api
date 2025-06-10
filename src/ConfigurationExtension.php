<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github;

use Siganushka\ApiFactory\Github\OAuth\AccessToken;
use Siganushka\ApiFactory\Github\OAuth\Client;
use Siganushka\ApiFactory\ResolverExtensionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationExtension implements ResolverExtensionInterface
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $fn = fn ($value, string $key) => $resolver->isDefined($key) && null !== $value;

        $configs = $this->configuration->toArray();
        $resolver->setDefaults(array_filter($configs, $fn, \ARRAY_FILTER_USE_BOTH));
    }

    public static function getExtendedClasses(): iterable
    {
        return [
            Client::class,
            AccessToken::class,
        ];
    }
}
