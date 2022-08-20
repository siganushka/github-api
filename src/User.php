<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\AbstractRequest;
use Siganushka\ApiClient\Exception\ParseResponseException;
use Siganushka\ApiClient\RequestOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps#3-use-the-access-token-to-access-the-api
 */
class User extends AbstractRequest
{
    public const URL = 'https://api.github.com/user';

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('access_token')
            ->required()
            ->allowedTypes('string')
        ;
    }

    protected function configureRequest(RequestOptions $request, array $options): void
    {
        $headers = [
            'Authorization' => sprintf('token %s', $options['access_token']),
        ];

        $request
            ->setMethod('GET')
            ->setUrl(static::URL)
            ->setHeaders($headers)
        ;
    }

    protected function parseResponse(ResponseInterface $response): array
    {
        $result = $response->toArray();
        if (isset($result['id'])) {
            return $result;
        }

        $error = (string) ($result['error'] ?? '0');
        $errorDescription = (string) ($result['error_description'] ?? 'error');

        throw new ParseResponseException($response, sprintf('%s (%s)', $errorDescription, $error));
    }
}
