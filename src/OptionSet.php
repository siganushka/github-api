<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github;

use Symfony\Component\OptionsResolver\OptionConfigurator;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OptionSet
{
    public static function client_id(OptionsResolver $resolver): OptionConfigurator
    {
        return $resolver
            ->define('client_id')
            ->required()
            ->allowedTypes('string')
        ;
    }

    public static function client_secret(OptionsResolver $resolver): OptionConfigurator
    {
        return $resolver
            ->define('client_secret')
            ->required()
            ->allowedTypes('string')
        ;
    }
}
