<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\AbstractConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Gitub configuration.
 */
class Configuration extends AbstractConfiguration
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        OptionsUtils::client_id($resolver);
        OptionsUtils::client_secret($resolver);
    }
}
