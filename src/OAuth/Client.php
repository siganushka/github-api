<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github\OAuth;

use Siganushka\ApiFactory\Github\ConfigurationExtension;
use Siganushka\ApiFactory\Github\OptionSet;
use Siganushka\ApiFactory\ResolverInterface;
use Siganushka\ApiFactory\ResolverTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Client implements ResolverInterface
{
    use ResolverTrait;

    public function __construct(
        private readonly ?HttpClientInterface $httpClient = null,
        private readonly ?CacheInterface $cache = null)
    {
    }

    /**
     * @see https://docs.github.com/zh/apps/oauth-apps/building-oauth-apps/authorizing-oauth-apps#1-request-a-users-github-identity
     */
    public function getRedirectUrl(array $options = []): string
    {
        $resolved = $this->resolve($options);
        $query = array_filter([
            'client_id' => $resolved['client_id'],
            'redirect_uri' => $resolved['redirect_uri'],
            'login' => $resolved['login'],
            'scope' => $resolved['scope'],
            'state' => $resolved['state'],
            'code_challenge' => $resolved['code_challenge'],
            'code_challenge_method' => $resolved['code_challenge_method'],
            'allow_signup' => $resolved['allow_signup'],
            'prompt' => $resolved['prompt'],
        ], static fn ($value) => null !== $value);

        ksort($query);

        return 'https://github.com/login/oauth/authorize?'.http_build_query($query);
    }

    public function getAccessToken(array $options = []): array
    {
        $accessToken = new AccessToken($this->httpClient, $this->cache);

        if (isset($this->extensions[ConfigurationExtension::class])) {
            $accessToken->extend($this->extensions[ConfigurationExtension::class]);
        }

        return $accessToken->send($options);
    }

    public function getUser(array $options = []): array
    {
        $user = new User($this->httpClient);

        return $user->send($options);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        OptionSet::client_id($resolver);
        OptionSet::client_secret($resolver);

        $resolver
            ->define('redirect_uri')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;

        $resolver
            ->define('login')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;

        $resolver
            ->define('scope')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;

        $resolver
            ->define('state')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;

        $resolver
            ->define('code_challenge')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;

        $resolver
            ->define('code_challenge_method')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;

        $resolver
            ->define('allow_signup')
            ->default(null)
            ->allowedValues(null, 'true', 'false')
        ;

        $resolver
            ->define('prompt')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;
    }
}
