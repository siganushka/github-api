<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github\OAuth;

use Siganushka\ApiFactory\AbstractRequest;
use Siganushka\ApiFactory\Exception\ParseResponseException;
use Siganushka\ApiFactory\Github\OptionSet;
use Siganushka\ApiFactory\RequestOptions;
use Siganushka\ApiFactory\Response\StaticResponse;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @extends AbstractRequest<array>
 */
class AccessToken extends AbstractRequest
{
    private readonly CacheInterface $cache;

    public function __construct(?HttpClientInterface $httpClient = null, ?CacheInterface $cache = null)
    {
        $this->cache = $cache ?? new NullAdapter();

        parent::__construct($httpClient);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        OptionSet::client_id($resolver);
        OptionSet::client_secret($resolver);

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

        $resolver
            ->define('code_verifier')
            ->default(null)
            ->allowedTypes('null', 'string')
        ;
    }

    /**
     * @see https://docs.github.com/zh/apps/oauth-apps/building-oauth-apps/authorizing-oauth-apps#2-users-are-redirected-back-to-your-site-by-github
     */
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
            'code_verifier' => $options['code_verifier'],
        ], static fn ($value) => null !== $value);

        $request
            ->setUrl('https://github.com/login/oauth/access_token')
            ->setMethod('POST')
            ->setHeaders($headers)
            ->setBody($body)
        ;
    }

    protected function sendRequest(RequestOptions $request): ResponseInterface
    {
        $callback = function (ItemInterface $item) use ($request): ResponseInterface {
            $item->expiresAfter(600);

            $response = parent::sendRequest($request);
            $parsedResponse = $this->parseResponse($response);

            return StaticResponse::createFromArray($parsedResponse);
        };

        return $this->cache->get($request->__toString(), $callback);
    }

    protected function parseResponse(ResponseInterface $response): array
    {
        $result = $response->toArray();
        if (isset($result['access_token'])) {
            return $result;
        }

        $error = (string) ($result['error'] ?? '0');
        $errorDescription = (string) ($result['error_description'] ?? 'error');

        throw new ParseResponseException($response, \sprintf('%s (%s)', $errorDescription, $error));
    }
}
