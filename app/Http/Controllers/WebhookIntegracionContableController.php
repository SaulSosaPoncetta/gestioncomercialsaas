<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * POST /webhooks/hub/integracion-contable  (firmado por el hub, ver VerificarFirmaHub)
 *
 * vincular:    { referencia_externa, contabilidad_url, contabilidad_token }
 *              Guarda la conexión, activa el envío automático y manda lo ya emitido en el mes.
 * desvincular: { referencia_externa }  → apaga el envío y borra el token.
 */
class WebhookIntegracionContableController extends Controller
{
    public function __invoke(Request $request)
    {
        $datos = $request->validate([
            'accion'             => 'required|in:vincular,desvincular',
            'referencia_externa' => 'required',
            'contabilidad_url'   => 'required_if:accion,vincular|nullable|url',
            'contabilidad_token' => 'required_if:accion,vincular|nullable|string|max:255',
        ]);

        $empresa = Empresa::find($datos['referencia_externa']);

        if (! $empresa) {
            return response()->json(['ok' => false, 'error' => 'la empresa no existe en GestiónComercial'], 404);
        }

        if ($datos['accion'] === 'desvincular') {
            $empresa->update(['contabilidad_activa' => false, 'contabilidad_token' => null]);

            return response()->json(['ok' => true]);
        }

        $empresa->update([
            'contabilidad_url'    => $datos['contabilidad_url'],
            'contabilidad_token'  => $datos['contabilidad_token'],
            'contabilidad_activa' => true,
            // Si nunca se configuró, arranca desde el 1° del mes (así el IVA del mes queda completo)
            'contabilidad_desde'  => $empresa->contabilidad_desde ?? now()->startOfMonth(),
        ]);

        // Envía lo emitido desde esa fecha que todavía no se mandó (después de responder al hub)
        $idEmpresa = $empresa->id_empresa;
        $desde = $empresa->contabilidad_desde->format('Y-m-d');
        dispatch(fn () => Artisan::call('contabilidad:sincronizar', ['--empresa' => $idEmpresa, '--desde' => $desde]))->afterResponse();

        return response()->json(['ok' => true]);
    }
}
