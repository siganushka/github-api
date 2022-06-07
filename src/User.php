<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\AbstractRequest;
use Siganushka\ApiClient\RequestOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps#3-use-the-access-token-to-access-the-api
 */
class User extends AbstractRequest
{
    public const URL = 'https://api.github.com/user';

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('access_token');
        $resolver->setAllowedTypes('access_token', 'string');
    }

    /**
     * @param array{ access_token: string } $options
     */
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

    /**
     * @return array<string, mixed>
     */
    public function parseResponse(ResponseInterface $response): array
    {
        return $response->toArray();
    }
}
