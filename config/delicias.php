<?php

return [
    'name' => 'Delicias Sn',
    'address' => env('DELICIAS_ADDRESS', 'Catamarca 523'),
    'whatsapp' => env('DELICIAS_WHATSAPP', '5493364012851'),
    'whatsapp_display' => env('DELICIAS_WHATSAPP_DISPLAY', '+54 9 336 401-2851'),
    'instagram' => env('DELICIAS_INSTAGRAM', '@delicias_.sn'),
    'hours' => env('DELICIAS_HOURS', "Lunes a viernes de 12hs a 20hs · Sábados y domingos de 9hs a 20hs"),
    'alias' => env('DELICIAS_ALIAS', '.demo'),
    'cbu' => env('DELICIAS_CBU', '0000003100000000000000'),
    'lead_times' => [
        'cookies' => 24,
        'cupcakes' => 48,
        'boxes' => 48,
        'tortas' => 72,
        'mesas-dulces' => 240,
    ],
];
