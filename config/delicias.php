<?php

return [
    'name' => 'Delicias Sn',
    'canonical_url' => rtrim(env('DELICIAS_CANONICAL_URL', 'https://deliciassn.com'), '/'),
    'christmas_theme_enabled' => filter_var(env('DELICIAS_CHRISTMAS_THEME', false), FILTER_VALIDATE_BOOL),
    'christmas_snow_enabled' => filter_var(env('DELICIAS_CHRISTMAS_SNOW', true), FILTER_VALIDATE_BOOL),
    'address' => env('DELICIAS_ADDRESS', 'Jujuy 422'),
    'whatsapp' => env('DELICIAS_WHATSAPP', '5493364012851'),
    'whatsapp_display' => env('DELICIAS_WHATSAPP_DISPLAY', '+54 9 336 401-2851'),
    'instagram' => env('DELICIAS_INSTAGRAM', '@delicias_.sn'),
    'hours' => env('DELICIAS_HOURS', 'Lunes a viernes de 13hs a 20hs · Sábados y domingos de 9hs a 20hs'),
    'ga4_id' => env('DELICIAS_GA4_ID'),
    'faqs' => [
        ['question' => '¿Cómo se reserva una fecha?', 'answer' => 'Confirmamos la fecha después de recibir tus datos y una seña del 50% del total. Luego se coordina la entrega o el retiro.'],
        ['question' => '¿Con cuánta anticipación tengo que pedir?', 'answer' => 'Los pedidos requieren entre 2 y 3 días de anticipación. Las tortas personalizadas y mesas dulces requieren 5 días de anticipación.'],
        ['question' => '¿A qué zonas hacen envíos?', 'answer' => 'Realizamos envíos en San Nicolás de los Arroyos. El costo varía según la zona.'],
        ['question' => '¿Puedo retirar mi pedido?', 'answer' => 'Sí, podés retirarlo por Jujuy 422 en la franja horaria que elegiste, con reserva previa.'],
        ['question' => '¿Qué pasa si necesito cancelar?', 'answer' => 'Avisanos con la mayor anticipación posible. Para revisar cambios o cancelaciones, escribinos por WhatsApp con tu número de pedido. La seña no es reembolsable.'],
        ['question' => '¿Tienen opciones sin alérgenos?', 'answer' => 'Todos nuestros productos se elaboran en el mismo obrador. Consultanos por ingredientes y posibles trazas antes de encargar.'],
        ['question' => '¿Cómo conservo lo que compré?', 'answer' => 'Cada producto incluye sus indicaciones. En general, las tortas y cupcakes van en heladera y las cookies en recipiente hermético.'],
    ],
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
