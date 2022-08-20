<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\RequestOptionsExtensionInterface;
use Siganushka\ApiClient\RequestOptionsExtensionTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurationOptions implements RequestOptionsExtensionInterface
{
    use RequestOptionsExtensionTrait;

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

    public static function getExtendedRequests(): array
    {
        return [
            AccessToken::class,
        ];
    }
}
