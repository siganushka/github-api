<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\User;

require __DIR__.'/_autoload.php';

// OAuth 授权通过 code 换取到的 access_token，每个 code 只能使用一次
$options = [
    'access_token' => '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',
];

$parsedResponse = $client->send(User::class, $options);
dd($parsedResponse);
