<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class OptionSet
{
    public static function client_id(OptionsResolver $resolver): void
    {
        $resolver
            ->define(__FUNCTION__)
            ->required()
            ->allowedTypes('string')
        ;
    }

    public static function client_secret(OptionsResolver $resolver): void
    {
        $resolver
            ->define(__FUNCTION__)
            ->required()
            ->allowedTypes('string')
        ;
    }
}
