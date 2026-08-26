<?php

namespace App\Services;

use App\Models\Empresa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Consulta el estado de pago de la empresa contra MiGestión Panel (el hub),
 * y cachea el resultado localmente para no pegarle a la API en cada login.
 * El webhook (WebhookEstadoClienteController) puede adelantarse a esta
 * verificación cuando el hub avisa un cambio antes de que venza la caché.
 */
class HubEstadoClienteService
{
    public function sincronizar(Empresa $empresa, bool $forzar = false): Empresa
    {
        if (! config('migestion_hub.url') || ! config('migestion_hub.api_key')) {
            // Todavía no configuraste la integración con el hub: no bloqueamos nada.
            return $empresa;
        }

        $cacheVencida = ! $empresa->fecha_verificacion_pago
            || $empresa->fecha_verificacion_pago->addHours((int) config('migestion_hub.cache_horas'))->isPast();

        if (! $forzar && ! $cacheVencida) {
            return $empresa;
        }

        try {
            $response = Http::withHeaders(['X-Api-Key' => config('migestion_hub.api_key')])
                ->timeout(5)
                ->get(rtrim(config('migestion_hub.url'), '/').'/api/estado-cliente', [
                    'referencia_externa' => (string) $empresa->id_empresa,
                ]);

            if ($response->status() === 404) {
                // Todavía no está vinculada en el hub: no la bloqueamos por eso.
                $empresa->fecha_verificacion_pago = now();
                $empresa->save();

                return $empresa;
            }

            $response->throw();
            $datos = $response->json();

            $empresa->estado_pago = match ($datos['estado'] ?? null) {
                'activa' => 'activa',
                'vencida' => 'vencida',
                'suspendida' => 'suspendida',
                'cancelada' => 'cancelada',
                default => $empresa->estado_pago,
            };
            $empresa->fecha_verificacion_pago = now();
            $empresa->save();
        } catch (\Throwable $e) {
            // Si el hub no responde, seguimos con el último estado conocido
            // en vez de trabar el login de todo el mundo por un problema de red.
            Log::warning('No se pudo verificar el estado de pago contra el hub', [
                'id_empresa' => $empresa->id_empresa,
                'error' => $e->getMessage(),
            ]);
        }

        return $empresa;
    }
}