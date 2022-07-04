<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\AccessToken;
use Siganushka\ApiClient\Github\Client;

require __DIR__.'/_autoload.php';

if (!isset($_GET['code'])) {
    $currentUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
        ($_SERVER['HTTP_HOST'] ?? 'localhost').
        ($_SERVER['REQUEST_URI'] ?? '');

    $options = [
        'redirect_uri' => $currentUrl,
    ];

    $client = new Client($configuration);
    $client->redirect($options);
    // dd($authorize->getRedirectUrl($options));

    exit;
}

$options = [
    'code' => $_GET['code'],
];

$parsedResponse = $client->send(AccessToken::class, $options);
dd($parsedResponse);
