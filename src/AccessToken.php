<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Psr\Cache\CacheItemPoolInterface;
use Siganushka\ApiClient\AbstractRequest;
use Siganushka\ApiClient\Exception\ParseResponseException;
use Siganushka\ApiClient\RequestOptions;
use Siganushka\ApiClient\Response\ResponseFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps#2-users-are-redirected-back-to-your-site-by-github
 */
class AccessToken extends AbstractRequest
{
    public const URL = 'https://github.com/login/oauth/access_token';

    private CacheItemPoolInterface $cachePool;
    private Configuration $configuration;

    public function __construct(CacheItemPoolInterface $cachePool, Configuration $configuration)
    {
        $this->cachePool = $cachePool;
        $this->configuration = $configuration;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('code');
        $resolver->setAllowedTypes('code', 'string');

        $resolver->setDefault('redirect_uri', null);
    }

    protected function configureRequest(RequestOptions $request, array $options): void
    {
        $headers = [
            'Accept' => 'application/json',
        ];

        $body = [
            'client_id' => $this->configuration['client_id'],
            'client_secret' => $this->configuration['client_secret'],
            'code' => $options['code'],
        ];

        if ($options['redirect_uri']) {
            $body['redirect_uri'] = $options['redirect_uri'];
        }

        $request
            ->setMethod('POST')
            ->setUrl(static::URL)
            ->setHeaders($headers)
            ->setBody($body)
        ;
    }

    protected function sendRequest(RequestOptions $request): ResponseInterface
    {
        $key = sprintf('%s_%s', __CLASS__, md5(serialize($request->toArray())));

        $cacheItem = $this->cachePool->getItem($key);
        if ($cacheItem->isHit()) {
            /** @var array{ access_token: string, token_type: string, scope: string } */
            $cacheData = $cacheItem->get();

            return ResponseFactory::createMockResponseWithJson($cacheData);
        }

        $response = parent::sendRequest($request);
        $parsedResponse = $this->parseResponse($response);

        $cacheItem->set($parsedResponse);
        $cacheItem->expiresAfter(600);
        $this->cachePool->save($cacheItem);

        return $response;
    }

    /**
     * @return array{ access_token: string, token_type: string, scope: string }
     */
    public function parseResponse(ResponseInterface $response): array
    {
        /**
         * @var array{
         *  access_token?: string,
         *  token_type: string,
         *  scope: string,
         *  error?: string,
         *  error_description?: string
         * }
         */
        $result = $response->toArray();
        if (isset($result['access_token'])) {
            return $result;
        }

        $error = (string) ($result['error'] ?? '');
        $errorDescription = (string) ($result['error_description'] ?? '');

        throw new ParseResponseException($response, sprintf('%s (%s)', $errorDescription, $error));
    }
}
