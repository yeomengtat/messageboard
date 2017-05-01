<?php
namespace Xodebox;

Config::$routerConfig = [
    'map' => [
        'url' => 'index',
        'controller' => 'App',
        'action'     => 'index'
    ],
    'fallback' => [
        '404' => 'error/404.html'
    ]
];

?>
