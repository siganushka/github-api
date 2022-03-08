<?php

declare(strict_types=1);

namespace Siganushka\ApiClient\Github;

use Siganushka\ApiClient\AbstractRequest;
use Siganushka\ApiClient\Exception\ParseResponseException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @see https://docs.github.com/cn/developers/apps/building-oauth-apps/authorizing-oauth-apps#2-users-are-redirected-back-to-your-site-by-github
 */
class AccessToken extends AbstractRequest
{
    public const URL = 'https://github.com/login/oauth/access_token';

    protected Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    protected function configureRequest(array $options): void
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

        $this
            ->setMethod('POST')
            ->setUrl(static::URL)
            ->setHeaders($headers)
            ->setBody($body)
        ;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('code');
        $resolver->setAllowedTypes('code', 'string');

        $resolver->setDefault('redirect_uri', null);
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
