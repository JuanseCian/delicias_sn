<?php

return [
    'name' => 'Delicias Sn',
    'address' => env('DELICIAS_ADDRESS', ' 523'),
    'whatsapp' => env('DELICIAS_WHATSAPP', '5493364012851'),
    'whatsapp_display' => env('DELICIAS_WHATSAPP_DISPLAY', '+54 9 336 401-2851'),
    'instagram' => env('DELICIAS_INSTAGRAM', '@delicias_.sn'),
    'hours' => env('DELICIAS_HOURS', 'Lunes a viernes de 13hs a 20hs · Sábados y domingos de 9hs a 20hs'),
    'time_slots' => [
        'weekday' => ['13 a 15 h', '15 a 18 h', '18 a 20 h'],
        'weekend' => ['9 a 12 h', '12 a 15 h', '15 a 18 h', '18 a 20 h'],
    ],
    'alias' => env('DELICIAS_ALIAS', 'delicias.sn'),
    'cbu' => env('DELICIAS_CBU', '4530000800014179160249'),
    'account_holder' => env('DELICIAS_ACCOUNT_HOLDER', 'Antonella Castonjauregui'),
    'cuil' => env('DELICIAS_CUIL', '27-46359377-4'),
    'lead_times' => [
        'cookies' => 24,
        'cupcakes' => 48,
        'boxes' => 48,
        'tortas' => 72,
        'mesas-dulces' => 240,
    ],
];
