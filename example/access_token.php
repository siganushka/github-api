<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\Authorize;
use Siganushka\ApiClient\Github\AccessToken;

require __DIR__.'/_autoload.php';

if (!isset($_GET['code'])) {
    $currentUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
        ($_SERVER['HTTP_HOST'] ?? 'localhost').
        ($_SERVER['REQUEST_URI'] ?? '');

    $options = [
        'redirect_uri' => $currentUrl,
    ];

    $authorize = new Authorize($configuration);
    $authorize->redirect($options);
    // dd($authorize->getAuthorizeUrl($options));

    exit;
}

$options = [
    'code' => $_GET['code'],
];

$parsedResponse = $client->send(AccessToken::class, $options);
dd($parsedResponse);
