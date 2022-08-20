<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Psr\Cache\CacheItemPoolInterface;
use Siganushka\ApiClient\ConfigurableSubjectInterface;
use Siganushka\ApiClient\ConfigurableSubjectTrait;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Gitub OAuth client class.
 *
 * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps
 */
class Client implements ConfigurableSubjectInterface
{
    use ConfigurableSubjectTrait;

    public const URL = 'https://github.com/login/oauth/authorize';

    private HttpClientInterface $httpClient;
    private CacheItemPoolInterface $cachePool;

    public function __construct(HttpClientInterface $httpClient = null, CacheItemPoolInterface $cachePool = null)
    {
        $this->httpClient = $httpClient ?? HttpClient::create();
        $this->cachePool = $cachePool ?? new FilesystemAdapter();
    }

    public function getRedirectUrl(array $options = []): string
    {
        $resolver = new OptionsResolver();
        $this->configure($resolver);

        $resolved = $resolver->resolve($options);

        $query = array_filter([
            'client_id' => $resolved['client_id'],
            'redirect_uri' => $resolved['redirect_uri'],
            'login' => $resolved['login'],
            'scope' => $resolved['scope'],
            'state' => $resolved['state'],
            'allow_signup' => $resolved['allow_signup'],
        ], fn ($value) => null !== $value);

        ksort($query);

        return static::URL.'?'.http_build_query($query);
    }

    public function getAccessToken(array $options = []): array
    {
        $accessToken = new AccessToken($this->cachePool);
        if (isset($this->configurators[ConfigurationOptions::class])) {
            $accessToken->using($this->configurators[ConfigurationOptions::class]);
        }

        return $accessToken->send($this->httpClient, $options);
    }

    public function getUser(array $options = []): array
    {
        $user = new User();

        return $user->send($this->httpClient, $options);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        OptionsUtils::client_id($resolver);
        OptionsUtils::client_secret($resolver);

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
            ->define('allow_signup')
            ->default(null)
            ->allowedValues(null, 'true', 'false')
        ;
    }
}
