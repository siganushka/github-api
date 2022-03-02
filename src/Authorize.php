<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\ConfigurableOptionsInterface;
use Siganushka\ApiClient\ConfigurableOptionsTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Authorize implements ConfigurableOptionsInterface
{
    use ConfigurableOptionsTrait;

    public const URL = 'https://github.com/login/oauth/authorize';

    protected Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param array<string, string> $options
     */
    public function getAuthorizeUrl(array $options = []): string
    {
        $resolved = $this->resolveOptions($options);
        $queryString = http_build_query($resolved);

        return static::URL.'?'.$queryString;
    }

    /**
     * @param array<string, string> $options
     */
    public function redirect(array $options = []): void
    {
        $authorizeUrl = $this->getAuthorizeUrl($options);
        if (class_exists(RedirectResponse::class)) {
            $response = new RedirectResponse($authorizeUrl);
            $response->send();
        }

        header(sprintf('Location: %s', $authorizeUrl));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('client_id', $this->configuration['client_id']);
        $resolver->setDefined(['redirect_uri', 'login', 'scope', 'state', 'allow_signup']);

        $resolver->setAllowedTypes('client_id', 'string');
        $resolver->setAllowedTypes('redirect_uri', 'string');
        $resolver->setAllowedTypes('login', 'string');
        $resolver->setAllowedTypes('scope', 'string');
        $resolver->setAllowedTypes('state', 'string');
        $resolver->setAllowedValues('allow_signup', ['true', 'false']);
    }
}
