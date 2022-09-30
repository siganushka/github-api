<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github\OAuth;

use Psr\Cache\CacheItemPoolInterface;
use Siganushka\ApiFactory\AbstractRequest;
use Siganushka\ApiFactory\Exception\ParseResponseException;
use Siganushka\ApiFactory\Github\OptionsUtils;
use Siganushka\ApiFactory\RequestOptions;
use Siganushka\ApiFactory\Response\CachedResponse;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps#2-users-are-redirected-back-to-your-site-by-github
 */
class AccessToken extends AbstractRequest
{
    public const URL = 'https://github.com/login/oauth/access_token';

    private CacheItemPoolInterface $cachePool;

    public function __construct(HttpClientInterface $httpClient = null, CacheItemPoolInterface $cachePool = null)
    {
        $this->cachePool = $cachePool ?? new FilesystemAdapter();

        parent::__construct($httpClient);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        OptionsUtils::client_id($resolver);
        OptionsUtils::client_secret($resolver);

        $resolver
            ->define('code')
            ->required()
            ->allowedTypes('string')
        ;

        $resolver
            ->define('redirect_uri')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;
    }

    protected function configureRequest(RequestOptions $request, array $options): void
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        $body = array_filter([
            'client_id' => $options['client_id'],
            'client_secret' => $options['client_secret'],
            'code' => $options['code'],
            'redirect_uri' => $options['redirect_uri'],
        ], fn ($value) => null !== $value);

        $request
            ->setMethod('POST')
            ->setUrl(static::URL)
            ->setHeaders($headers)
            ->setBody($body)
        ;
    }

    protected function sendRequest(RequestOptions $request): ResponseInterface
    {
        $cacheItem = $this->cachePool->getItem((string) $request);
        if ($cacheItem->isHit()) {
            return CachedResponse::createFromJson($cacheItem->get());
        }

        $response = parent::sendRequest($request);
        $parsedResponse = $this->parseResponse($response);

        $cacheItem->set($parsedResponse);
        $cacheItem->expiresAfter(600);
        $this->cachePool->save($cacheItem);

        return $response;
    }

    protected function parseResponse(ResponseInterface $response): array
    {
        $result = $response->toArray();
        if (isset($result['access_token'])) {
            return $result;
        }

        $error = (string) ($result['error'] ?? '0');
        $errorDescription = (string) ($result['error_description'] ?? 'error');

        throw new ParseResponseException($response, sprintf('%s (%s)', $errorDescription, $error));
    }
}
