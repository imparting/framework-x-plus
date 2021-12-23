<?php

namespace app\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ApiMiddleware
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