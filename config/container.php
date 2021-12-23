<?php

return [
    'db.config' => config('database'),
    React\Cache\CacheInterface::class => React\Cache\ArrayCache::class,
    Psr\Http\Message\ResponseInterface::class => React\Http\Message\Response::class,
];
