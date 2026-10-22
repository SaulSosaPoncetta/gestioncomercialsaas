<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\SincronizacionContable;
use App\Services\ContabilidadClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/** Configuración › Integración contable (por empresa del usuario) */
class IntegracionContableController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $this->empresa();

        $registros = SincronizacionContable::where('id_empresa', $empresa->id_empresa)
            ->when($request->estado, fn ($q, $e) => $q->where('estado', $e))
            ->latest('updated_at')
            ->paginate(25)
            ->withQueryString();

        $conteo = SincronizacionContable::where('id_empresa', $empresa->id_empresa)
            ->selectRaw('estado, COUNT(*) AS cantidad')->groupBy('estado')->pluck('cantidad', 'estado');

        return view('integracion-contable.index', compact('empresa', 'registros', 'conteo'));
    }

    public function update(Request $request)
    {
        $empresa = $this->empresa();

        $datos = $request->validate([
            'contabilidad_url'   => ['nullable', 'url', 'max:255'],
            'contabilidad_token' => ['nullable', 'string', 'max:200'],
            'contabilidad_desde' => ['nullable', 'date'],
        ]);

        $empresa->contabilidad_url = $datos['contabilidad_url'] ?? null;
        $empresa->contabilidad_desde = $datos['contabilidad_desde'] ?? null;
        $empresa->contabilidad_activa = $request->boolean('contabilidad_activa');
        $empresa->contabilidad_compras = $request->boolean('contabilidad_compras');

        // El token solo se reemplaza si se escribe uno nuevo
        if (! empty($datos['contabilidad_token'])) {
            $empresa->contabilidad_token = trim($datos['contabilidad_token']);
        }
        if ($request->boolean('quitar_token')) {
            $empresa->contabilidad_token = null;
            $empresa->contabilidad_activa = false;
        }

        $empresa->save();

        return redirect()->route('integracion-contable.index')->with('success', 'Configuración guardada.');
    }

    public function probar(ContabilidadClient $cliente)
    {
        $r = $cliente->probar($this->empresa());

        return back()->with($r['ok'] ? 'success' : 'error', $r['mensaje']);
    }

    /** Reenvía un documento puntual */
    public function reenviar(SincronizacionContable $sincronizacion, ContabilidadClient $cliente)
    {
        abort_unless($sincronizacion->id_empresa === $this->empresa()->id_empresa, 404);

        $r = $cliente->sincronizar($sincronizacion->tipo_origen, (int) $sincronizacion->id_externo);

        return back()->with($r?->estado === 'ok' ? 'success' : 'error', $r?->mensaje ?? 'La integración no está activa.');
    }

    /** Envía los pendientes y reintenta los errores (desde la fecha elegida) */
    public function sincronizar(Request $request)
    {
        $empresa = $this->empresa();
        $request->validate(['desde' => ['nullable', 'date']]);

        if (! $empresa->integraConContabilidad()) {
            return back()->with('error', 'Active la integración y cargue URL y token antes de sincronizar.');
        }

        @set_time_limit(300);
        Artisan::call('contabilidad:sincronizar', array_filter([
            '--empresa' => $empresa->id_empresa,
            '--desde'   => $request->desde,
        ]));

        $ultima = collect(explode("\n", trim(Artisan::output())))->last();

        return back()->with('success', $ultima ?: 'Sincronización terminada.');
    }

    private function empresa(): Empresa
    {
        $empresa = auth()->user()->empresa;
        abort_unless($empresa, 403, 'Su usuario no está asociado a una empresa.');

        return $empresa;
    }
}
