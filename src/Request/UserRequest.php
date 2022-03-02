<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github\Request;

use Siganushka\ApiClient\AbstractRequest;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\ResponseInterface;

class UserRequest extends AbstractRequest
{
    public const URL = 'https://api.github.com/user';

    /**
     * @param array{ access_token: string } $options
     */
    protected function configureRequest(array $options): void
    {
        $headers = [
            'Authorization' => sprintf('token %s', $options['access_token']),
        ];

        $this
            ->setMethod('GET')
            ->setUrl(static::URL)
            ->setHeaders($headers)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('access_token');
        $resolver->setAllowedTypes('access_token', 'string');
    }

    /**
     * @return array<string, mixed>
     */
    public function parseResponse(ResponseInterface $response): array
    {
        return $response->toArray();
    }
}
