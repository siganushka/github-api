<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\Authorize;
use Siganushka\ApiClient\Github\Request\AccessTokenRequest;

require __DIR__.'/_autoload.php';

if (!isset($_GET['code'])) {
    $currentUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
        ($_SERVER['HTTP_HOST'] ?? 'localhost').
        ($_SERVER['REQUEST_URI'] ?? '');

    $options = ['redirect_uri' => $currentUrl];

    $authorize = new Authorize($configuration);
    $authorize->redirect($options);
    // $authorizeUrl = $authorize->getAuthorizeUrl($options);
    // dd($authorizeUrl);

    exit;
}

$options = [
    'code' => $_GET['code'],
];

$result = $client->send(AccessTokenRequest::class, $options);
dd($result);
