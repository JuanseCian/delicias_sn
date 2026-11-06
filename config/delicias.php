<?php

return [
    'name' => 'Delicias Sn',
    'address' => env('DELICIAS_ADDRESS', 'Jujuy 422'),
    'whatsapp' => env('DELICIAS_WHATSAPP', '5493364012851'),
    'whatsapp_display' => env('DELICIAS_WHATSAPP_DISPLAY', '+54 9 336 401-2851'),
    'instagram' => env('DELICIAS_INSTAGRAM', '@delicias.sn'),
    'hours' => env('DELICIAS_HOURS', 'Lunes a sábado · 9 a 20 h'),
    'alias' => env('DELICIAS_ALIAS', 'DELicias.sn.demo'),
    'cbu' => env('DELICIAS_CBU', '0000003100000000000000'),
    'lead_times' => [
        'cookies' => 24,
        'cupcakes' => 48,
        'boxes' => 48,
        'tortas' => 72,
        'mesas-dulces' => 240,
    ],
];
