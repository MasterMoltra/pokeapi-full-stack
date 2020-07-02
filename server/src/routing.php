<?php

return [
    '/' => [
        'name' => 'home',
    ],
    '/dash' => [
        'name' => 'dash',
    ],
    '/php/pokeinfos' => [
        'name' => 'Controller:infosByPath',
        'args' => ['path', 'metadata'],
        'allowed_methods' => ['POST'],
    ],
];
