<?php

namespace app\middlewares;

use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\ValidationException;

class ValidatorMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        try {
            return $next($request);
        } catch (ValidationException $validationException) {
            return fail($validationException->getMessage(), null, 2000);
        }
    }
}