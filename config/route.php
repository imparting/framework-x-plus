<?php

use support\Route;

$middlewares = [
    app\middlewares\ApiMiddleware::class,
    app\middlewares\ValidatorMiddleware::class
];

Route::group('api', function () {
    Route::get('user', app\controller\IndexController::class);
    Route::get('test', [new app\controller\TestController(), 'index']);
}, $middlewares);
