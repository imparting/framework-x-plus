<?php

namespace app\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ThrowableMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return $next($request);
        } catch (Throwable $throwable) {
            return fail($throwable->getMessage());
        }
    }
}