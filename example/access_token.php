<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\Authorize;
use Siganushka\ApiClient\Github\AccessToken;
use Siganushka\ApiClient\Wechat\GenericUtils;

require __DIR__.'/_autoload.php';

if (!isset($_GET['code'])) {
    $options = [
        'redirect_uri' => GenericUtils::getCurrentUrl(),
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
