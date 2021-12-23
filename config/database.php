<?php
return [
    // 默认数据库
    'default' => 'mysql',

    // 各种数据库配置
    'connections' => [
        'mysql' => [
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'tests',
            'username'    => 'root',
            'password'    => '',
            'charset'     => 'utf8mb4',//utf8
            'timeout'     => 3,
            'table_prefix'=> '',
        ],
    ],
];