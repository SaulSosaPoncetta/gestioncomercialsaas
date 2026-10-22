<?php

return [
    // URL del sistema contable de MiGestión (sin barra final), ej: https://contable.migestion.com.ar
    // Cada empresa puede tener su propia URL en Configuración › Integración contable; esta es la de por defecto.
    'url' => env('CONTABILIDAD_URL'),

    // Segundos de espera por cada envío
    'timeout' => env('CONTABILIDAD_TIMEOUT', 10),
];
