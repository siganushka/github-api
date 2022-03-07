<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\Request\UserRequest;

require __DIR__.'/_autoload.php';

$options = [
    'access_token' => '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',
];

$result = $client->send(UserRequest::class, $options);
dd($result);
