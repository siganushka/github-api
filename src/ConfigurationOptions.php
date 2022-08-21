<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\Github\OAuth\AccessToken;
use Siganushka\ApiClient\Github\OAuth\Client;
use Siganushka\ApiClient\OptionsExtensionInterface;
use Siganushka\ApiClient\OptionsExtensionTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationOptions implements OptionsExtensionInterface
{
    use OptionsExtensionTrait;

    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        foreach ($this->configuration as $key => $value) {
            $resolver->setDefault($key, $value);
        }
    }

    public static function getExtendedClasses(): array
    {
        return [
            Client::class,
            AccessToken::class,
        ];
    }
}
