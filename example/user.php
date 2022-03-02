<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\Request\UserRequest;

require __DIR__.'/_autoload.php';

$options = [
    'access_token' => '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',
];

$wrappedResponse = $client->send(UserRequest::class, $options);
dd($wrappedResponse->getParsedBody());
