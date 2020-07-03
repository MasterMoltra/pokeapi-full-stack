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
        'args' => ['name', 'metadata'],
        // Add 'GET' in allowed_methods to allow e.g. php/pokeinfos?name=Seismitoad
        'allowed_methods' => ['POST'],
    ],
];
