<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\User;

require __DIR__.'/_autoload.php';

$options = [
    'access_token' => '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',
];

$result = $client->send(User::class, $options);
dd($result);
