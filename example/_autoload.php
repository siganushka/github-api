<?php

declare(strict_types=1);

use Siganushka\ApiClient\Github\Configuration;
use Siganushka\ApiClient\Github\GithubExtension;
use Siganushka\ApiClient\RequestClient;
use Siganushka\ApiClient\RequestClientBuilder;
use Siganushka\ApiClient\RequestFactoryBuilder;
use Symfony\Component\ErrorHandler\Debug;

require __DIR__.'/../vendor/autoload.php';

Debug::enable();

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        var_dump($vars);
        exit;
    }
}

$configFile = __DIR__.'/_config.php';
if (!is_file($configFile)) {
    exit('请复制 _config.php.dist 为 _config.php 并填写参数！');
}

$configs = require $configFile;
$configuration = new Configuration($configs);

$client = RequestClientBuilder::create()
    ->addExtension(new GithubExtension($configuration))
    ->build()
;
