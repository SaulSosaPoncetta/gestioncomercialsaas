<?php

namespace App\Services;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Da de alta la empresa en MiGestión Panel (el hub). Con el CUIT, el hub la reconoce
 * si ya usa la Contabilidad y vincula los dos sistemas solo.
 * Si el hub no está configurado o no responde, no frena nada (se puede reintentar con
 * php artisan hub:registrar-empresas).
 */
class HubClientes
{
    public function registrar(?Empresa $empresa, ?string $emailAdministrador = null): bool
    {
        if (! $empresa || ! config('migestion_hub.url') || ! config('migestion_hub.api_key')) {
            return false;
        }

        $email = $emailAdministrador ?: $this->emailAdministrador($empresa) ?: $empresa->email;

        if (! $email) {
            return false;
        }

        $datos = [
            'referencia_externa'    => (string) $empresa->id_empresa,
            'nombre'                => $empresa->razon_social,
            'email'                 => $email,
            'identificacion_fiscal' => $empresa->identificacion_fiscal,
            'telefono'              => $empresa->telefono,
        ];

        if (config('migestion_hub.plan') && config('migestion_hub.monto') !== null) {
            $datos['plan'] = config('migestion_hub.plan');
            $datos['monto'] = (float) config('migestion_hub.monto');
        }

        try {
            Http::withHeaders(['X-Api-Key' => config('migestion_hub.api_key')])
                ->acceptJson()
                ->timeout(8)
                ->post(rtrim(config('migestion_hub.url'), '/') . '/api/registrar-cliente', $datos)
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::warning('No se pudo registrar la empresa en el hub', ['id_empresa' => $empresa->id_empresa, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /** Email del primer administrador de la empresa. */
    private function emailAdministrador(Empresa $empresa): ?string
    {
        return User::where('id_empresa', $empresa->id_empresa)
            ->whereHas('roles', fn ($q) => $q->where('name', 'admin'))
            ->orderBy('id')
            ->value('email')
            ?? User::where('id_empresa', $empresa->id_empresa)->orderBy('id')->value('email');
    }
}
