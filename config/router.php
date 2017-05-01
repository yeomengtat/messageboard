<?php
namespace Xodebox;

Config::$routerConfig = [
    'map' => [
        [
            'url' => 'index',
            'controller' => 'App',
            'action'     => 'index'
        ],
        
        [
            'url' => 'login',
            'controller' => 'App',
            'action'     => 'login'
        ],
        
        [
            'url' => 'register',
            'controller' => 'App',
            'action'     => 'register'
        ],

        

        
    ],
    'fallback' => [
        '404' => 'error/404.html'
    ]
];

?>
