<?php

namespace app\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use Throwable;

class ThrowableMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $throwable) {
            return fail($throwable->getMessage());
        }
    }
}