<?php

namespace support;

class Route
{
    private static $middlewares = [];
    private static $group = '/';
    /**
     * @var App $app
     */
    private static $app;

    public static function init(App $app)
    {
        if (!static::$app) static::$app = $app;
    }

    public static function group($group, $callable, $middlewares = [])
    {
        if (is_callable($callable)) {
            static::$group = $group;
            static::middlewares($middlewares);
            $callable();
            static::$middlewares = [];
            static::$group = '/';
        }
    }

    public static function get(string $route, $handler): void
    {
        static::map(['GET'], $route, $handler);
    }

    public static function head(string $route, $handler): void
    {
        static::map(['HEAD'], $route, $handler);
    }

    public static function post(string $route, $handler): void
    {
        static::map(['POST'], $route, $handler);
    }

    public static function put(string $route, $handler): void
    {
        static::map(['PUT'], $route, $handler);
    }

    public static function patch(string $route, $handler): void
    {
        static::map(['PATCH'], $route, $handler);
    }

    public static function delete(string $route, $handler): void
    {
        static::map(['DELETE'], $route, $handler);
    }

    public static function options(string $route, $handler): void
    {
        static::map(['OPTIONS'], $route, $handler);
    }

    public static function any(string $route, $handler): void
    {
        static::map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route, $handler);
    }

    public static function map($methods, $route, $handler)
    {
        if (is_string($handler) && str_contains($handler, '@')) {
            $callable = explode('@', $handler);
            $callable[0] = static::$app->container->get($callable[0]);
            $handler = $callable;
        } elseif (is_string($handler)) {
            $handler = static::$app->container->get($handler);
        }
        $route = str_replace('//', '/', '/' . static::$group . '/' . $route);
        $handlers = array_merge(static::$middlewares, [$handler]);
        static::$app->map($methods, $route, ...$handlers);
    }

    private static function middlewares($middlewares)
    {
        if (is_array($middlewares)) {
            foreach ($middlewares as $middleware) {
                static::middlewares($middleware);
            }
        } elseif (is_string($middlewares) && class_exists($middlewares)) {
            static::$middlewares[] = $middlewares;
        }
    }
}