<?php

declare(strict_types=1);

namespace Siganushka\ApiFactory\Github\OAuth;

use Siganushka\ApiFactory\AbstractRequest;
use Siganushka\ApiFactory\Exception\ParseResponseException;
use Siganushka\ApiFactory\RequestOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @extends AbstractRequest<array>
 */
class User extends AbstractRequest
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('access_token')
            ->required()
            ->allowedTypes('string')
        ;
    }

    /**
     * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps#3-use-the-access-token-to-access-the-api
     */
    protected function configureRequest(RequestOptions $request, array $options): void
    {
        $headers = [
            'Authorization' => \sprintf('token %s', $options['access_token']),
        ];

        $request
            ->setUrl('https://api.github.com/user')
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

        throw new ParseResponseException($response, \sprintf('%s (%s)', $errorDescription, $error));
    }
}
