<?php

namespace support;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;

class AsyncMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface|\Generator|ResponseInterface
    {
        $response = $next($request);
        if ($response instanceof PromiseInterface) {
            return $response->then(function (ResponseInterface $response) {
                return $this->handle($response);
            });
        } elseif ($response instanceof \Generator) {
            return (function () use ($response) {
                return $this->handle(yield from $response);
            })();
        } else {
            return $this->handle($response);
        }
    }

    private function handle($response): ResponseInterface
    {
        return $response instanceof ResponseInterface ? $response : ok($response);
    }
}