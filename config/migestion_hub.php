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
];