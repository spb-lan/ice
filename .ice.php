<?php

return [
    'vendor' => 'ifacesoft',
    'name' => 'ice',
    'namespace' => 'Ifacesoft\Ice\Framework\\',
    'alias' => 'Ice',
    'description' => 'Ice Framework',
    'url' => 'http://ice.ifacesoft.iceframework.net',
    'type' => 'module',
    'context' => '',
    'pathes' => [
        'config' => 'config/',
        'source' => 'source/',
        'resource' => 'resource/',
    ],
    'environments' => [
        'prod' => [
            'pattern' => '/^ice\.prod\.local$/',
        ],
        'test' => [
            'pattern' => '/^ice\.test\.local$/',
        ],
        'dev' => [
            'pattern' => '/^ice\.dev\.local$/'
        ],
    ],
    'modules' => [
        'spb-lan/ice-cli-fork' => [],
        'spb-lan/ice-http-fork' => [],
    ],
];