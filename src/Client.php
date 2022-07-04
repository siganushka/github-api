<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\ConfigurableOptionsInterface;
use Siganushka\ApiClient\ConfigurableOptionsTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Gitub OAuth client class.
 *
 * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps
 */
class Client implements ConfigurableOptionsInterface
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
    public function getRedirectUrl(array $options = []): string
    {
        $resolved = $this->resolve($options);
        $resolved['client_id'] = $this->configuration['client_id'];

        return static::URL.'?'.http_build_query($resolved);
    }

    /**
     * @param array<string, string> $options
     */
    public function redirect(array $options = []): void
    {
        $redirectUrl = $this->getRedirectUrl($options);
        if (class_exists(RedirectResponse::class)) {
            $response = new RedirectResponse($redirectUrl);
            $response->send();
        }

        header(sprintf('Location: %s', $redirectUrl));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['redirect_uri', 'login', 'scope', 'state', 'allow_signup']);

        $resolver->setAllowedTypes('redirect_uri', 'string');
        $resolver->setAllowedTypes('login', 'string');
        $resolver->setAllowedTypes('scope', 'string');
        $resolver->setAllowedTypes('state', 'string');
        $resolver->setAllowedValues('allow_signup', ['true', 'false']);
    }
}
