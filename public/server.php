<?php

use support\Config;

require __DIR__ . '/../vendor/autoload.php';

Config::load(config_path(), ['route', 'middlewares', 'container']);

$app = new support\App();
$app->setContentType('json');
$app->run();