<?php

return [
    // URL del panel central donde vive MiGestión Panel (panel-migestion)
    'url' => env('MIGESTION_HUB_URL'),

    // API key que te generó el hub al dar de alta este sistema (GestiónComercial)
    'api_key' => env('MIGESTION_HUB_API_KEY'),

    // Secret que el hub usa para firmar los webhooks que te manda a vos
    'webhook_secret' => env('MIGESTION_HUB_WEBHOOK_SECRET'),

    // Cuánto tiempo confiar en el último estado verificado antes de
    // volver a consultarle al hub en el próximo login
    'cache_horas' => env('MIGESTION_HUB_CACHE_HORAS', 6),

    // Segundos de validez de una orden firmada del hub (evita que se reenvíe más tarde)
    'tolerancia_segundos' => 300,

    // Opcional: plan y monto con que se crea la suscripción en el hub al registrarse una empresa
    // (si quedan vacíos, la suscripción la cargás vos desde el panel)
    'plan'  => env('MIGESTION_HUB_PLAN'),
    'monto' => env('MIGESTION_HUB_MONTO'),
];