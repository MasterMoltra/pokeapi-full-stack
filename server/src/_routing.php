<?php

return [
    '/' => [
        'name' => 'home',
        'template_vars' => [
            'msg' => '<em>Welcome!</em> ',
        ],
    ],
    '/dash' => [
        'name' => 'dash',
    ],
    '/api' => [
        'name' => 'Controller:htmlStaticSeoByPath',
        'args' => ['name'],
        'allowed_methods' => ['GET'],
    ],
    '/php/pokeinfos' => [
        'name' => 'Controller:jsonInfosByPath',
        'args' => ['name', 'metadata'],
        // Add 'GET' in allowed_methods to allow e.g. php/pokeinfos?name=Seismitoad
        'allowed_methods' => ['POST'],
    ],
];