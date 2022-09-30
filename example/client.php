<?php

declare(strict_types=1);

use Siganushka\ApiFactory\Github\ConfigurationExtension;
use Siganushka\ApiFactory\Github\OAuth\Client;

require __DIR__.'/_autoload.php';

$client = new Client();
$client->extend(new ConfigurationExtension($configuration));

if (!isset($_GET['code'])) {
    $currentUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
        ($_SERVER['HTTP_HOST'] ?? 'localhost').
        ($_SERVER['REQUEST_URI'] ?? '');

    $options = [
        'redirect_uri' => $currentUrl,
    ];

    $redirectUrl = $client->getRedirectUrl($options);
    header(sprintf('Location: %s', $redirectUrl));
    exit;
}

// 获取已授权用户令牌
$result = $client->getAccessToken([
    'code' => $_GET['code'],
]);
dump('用户令牌：', $result);

// 根据已授权用户令牌获取用户信息
$result = $client->getUser([
    'access_token' => $result['access_token'],
]);
dump('用户信息：', $result);
