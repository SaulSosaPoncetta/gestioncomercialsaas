<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookEstadoClienteController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->all();
        $firmaRecibida = $request->header('X-MiGestion-Signature', '');
        $firmaEsperada = hash_hmac('sha256', json_encode($payload), config('migestion_hub.webhook_secret'));

        if (! $firmaEsperada || ! hash_equals($firmaEsperada, $firmaRecibida)) {
            Log::warning('Webhook del hub con firma inválida', ['payload' => $payload]);

            return response()->json(['error' => 'firma inválida'], 401);
        }

        $request->validate([
            'referencia_externa' => 'required|string',
            'estado' => 'required|in:activa,vencida,suspendida,cancelada',
        ]);

        $empresa = Empresa::find($request->referencia_externa);

        if (! $empresa) {
            Log::warning('Webhook del hub para empresa inexistente', ['payload' => $payload]);

            return response()->json(['error' => 'empresa no encontrada'], 404);
        }

        $empresa->update([
            'estado_pago' => $request->estado,
            'fecha_verificacion_pago' => now(),
        ]);

        return response()->json(['ok' => true]);
    }
}