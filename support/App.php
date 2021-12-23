<?php

namespace support;

use DI\Container as ContainerAlias;
use Exception;

class App extends \FrameworkX\App
{
    public static string $contentType = 'text';
    /**
     * @var ContainerAlias $container
     */
    public ContainerAlias $container;

    public function __construct()
    {
        $this->initContainer();//容器
        $middlewares = array_merge(
            $this->asyncMiddlewares(),//异步中间件
            $this->initMiddlewares()//全局中间件
        );
        parent::__construct(...$middlewares);
        $this->initRoute();
    }

    public function setContentType($contentType)
    {
        if (in_array($contentType, ['text', 'json', 'xml', 'jsonp']))
            static::$contentType = $contentType;
    }

    private function initRoute()
    {
        Route::init($this);
        $route_file = config_path() . '/route.php';
        if (file_exists($route_file)) include_once $route_file;
    }

    /**
     * @throws Exception
     */
    private function initContainer()
    {
        $config = include_once config_path() . '/container.php';
        $this->container = Container::create($config);
        //return [new \FrameworkX\Container($config)];
    }

    /**
     * @return AsyncMiddleware[]
     */
    private function asyncMiddlewares()
    {
        return [new AsyncMiddleware()];
    }

    private function initMiddlewares()
    {
        $middlewares = [];
        $middlewares_file = config_path() . '/middlewares.php';
        if (file_exists($middlewares_file)) {
            $global_middlewares = include_once $middlewares_file;
            if (is_array($global_middlewares)) {
                foreach ($global_middlewares as $middleware) {
                    if (class_exists($middleware)) $middlewares[] = $middleware;
                }
            }
        }
        return $middlewares;
    }
}