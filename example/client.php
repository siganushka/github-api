<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\ConfigurationOptions;
use Siganushka\ApiClient\Github\OAuth\Client;

require __DIR__.'/_autoload.php';

$client = new Client();
$client->extend(new ConfigurationOptions($configuration));

if (!isset($_GET['code'])) {
    $currentUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://').
        ($_SERVER['HTTP_HOST'] ?? 'localhost').
        ($_SERVER['REQUEST_URI'] ?? '');

    $options = [
        'redirect_uri' => $currentUrl,
        'login' => 'login_value',
        'scope' => 'scope_value',
        'state' => 'state_value',
        'allow_signup' => 'true',
    ];

    $redirectUrl = $client->getRedirectUrl($options);
    header(sprintf('Location: %s', $redirectUrl));
    exit;
}

// 获取已授权用户的 access_token
$accessToken = $client->getAccessToken([
    'code' => $_GET['code'],
]);
dump('getAccessToken', $accessToken);

// 根据已授权用户的 access_token 获取用户信息
$user = $client->getUser([
    'access_token' => $accessToken['access_token'],
]);
dump('user', $user);
