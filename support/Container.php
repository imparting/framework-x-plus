<?php

namespace support;

use DI\ContainerBuilder;
use Exception;

class Container
{
    /**
     * @param $config
     * @return \DI\Container
     * @throws Exception
     */
    public static function create($config): \DI\Container
    {
        $builder = new ContainerBuilder();
        $builder->useAnnotations(true);
        $builder->useAutowiring(false);
        $builder->enableCompilation(runtime_path() . '/container');
        $builder->writeProxiesToFile(true, runtime_path() . '/container/proxies');
        $builder->addDefinitions($config);
        return $builder->build();
    }
}