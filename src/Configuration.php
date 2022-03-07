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
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('client_id');
        $resolver->setRequired('client_secret');
    }
}
