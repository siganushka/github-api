<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\AccessToken;
use Siganushka\ApiClient\Github\Configuration;
use Siganushka\ApiClient\Github\User;
use Siganushka\ApiClient\RequestClient;
use Siganushka\ApiClient\RequestRegistry;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpClient\HttpClient;

$configFile = __DIR__.'/_config.php';
if (!is_file($configFile)) {
    exit('请复制 _config.php.dist 为 _config.php 并填写参数！');
}

require $configFile;
Debug::enable();

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        var_dump($vars);
        exit;
    }
}

$httpClient = HttpClient::create();
$cachePool = new FilesystemAdapter();

$configuration = new Configuration([
    'client_id' => CLIENT_ID,
    'client_secret' => CLIENT_SECRET,
]);

$requests = [
    new AccessToken($cachePool, $configuration),
    new User(),
];

$registry = new RequestRegistry($httpClient, $requests);
$client = new RequestClient($registry);
