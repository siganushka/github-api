<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github;

use Siganushka\ApiFactory\AbstractConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Github Configuration.
 */
class Configuration extends AbstractConfiguration
{
    public static function configureOptions(OptionsResolver $resolver): void
    {
        OptionSet::client_id($resolver);
        OptionSet::client_secret($resolver);
    }
}
