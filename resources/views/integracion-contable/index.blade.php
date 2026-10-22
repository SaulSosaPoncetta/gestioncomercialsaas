@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-journal-arrow-up me-2"></i>Integración contable</h3>

@foreach(['success' => 'success', 'error' => 'danger'] as $k => $c)
    @if(session($k))
        <div class="alert alert-{{ $c }} alert-dismissible fade show">{{ session($k) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
@endforeach
@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Conexión con MiGestión Contable</div>
            <div class="card-body">
                <form method="POST" action="{{ route('integracion-contable.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">URL del sistema contable</label>
                        <input type="url" name="contabilidad_url" value="{{ old('contabilidad_url', $empresa->contabilidad_url) }}" class="form-control"
                               placeholder="{{ config('contabilidad.url') ?: 'https://contable.migestion.com.ar' }}">
                        @if(config('contabilidad.url'))<div class="form-text">Vacío = {{ config('contabilidad.url') }}</div>@endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Token de acceso</label>
                        <input type="password" name="contabilidad_token" class="form-control font-monospace" autocomplete="off"
                               placeholder="{{ $empresa->contabilidad_token ? '•••••••• (configurado — escriba uno nuevo para reemplazarlo)' : 'mgc_…' }}">
                        <div class="form-text">Se genera en el sistema contable: Configuración › Integraciones.</div>
                        @if($empresa->contabilidad_token)
                            <div class="form-check mt-1">
                                <input class="form-check-input" type="checkbox" name="quitar_token" value="1" id="quitar">
                                <label class="form-check-label small" for="quitar">Quitar el token</label>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enviar documentos emitidos desde</label>
                        <input type="date" name="contabilidad_desde" value="{{ old('contabilidad_desde', $empresa->contabilidad_desde?->format('Y-m-d')) }}" class="form-control" style="max-width: 200px;">
                        <div class="form-text">Los documentos anteriores no se envían (conviene usar el inicio del ejercicio contable).</div>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="contabilidad_activa" value="1" id="activa" @checked($empresa->contabilidad_activa)>
                        <label class="form-check-label" for="activa"><strong>Enviar automáticamente</strong> las facturas y notas de crédito de venta</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="contabilidad_compras" value="1" id="compras" @checked($empresa->contabilidad_compras)>
                        <label class="form-check-label" for="compras">Enviar también las <strong>compras</strong> y sus notas de crédito</label>
                        <div class="form-text">GestiónComercial no registra el IVA de las compras: en las facturas A se estima con la alícuota de cada producto y queda para revisar. Si importa las compras desde «Mis Comprobantes» de ARCA en la contabilidad, deje esto desactivado.</div>
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar</button>
                </form>
                <form method="POST" action="{{ route('integracion-contable.probar') }}" class="d-inline">
                    @csrf
                    <button class="btn btn-outline-secondary mt-2"><i class="bi bi-plug me-1"></i>Probar conexión</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Sincronización</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge text-bg-success">Enviados: {{ $conteo['ok'] ?? 0 }}</span>
                    <span class="badge text-bg-danger">Con error: {{ $conteo['error'] ?? 0 }}</span>
                    <span class="badge text-bg-secondary">Omitidos: {{ $conteo['omitido'] ?? 0 }}</span>
                </div>
                <form method="POST" action="{{ route('integracion-contable.sincronizar') }}">
                    @csrf
                    <label class="form-label small mb-1">Enviar lo pendiente desde</label>
                    <div class="input-group mb-2">
                        <input type="date" name="desde" value="{{ $empresa->contabilidad_desde?->format('Y-m-d') }}" class="form-control">
                        <button class="btn btn-outline-primary"><i class="bi bi-arrow-repeat me-1"></i>Sincronizar</button>
                    </div>
                    <div class="form-text">Envía los documentos no enviados desde esa fecha y reintenta los que dieron error. Lo ya enviado no se duplica.</div>
                </form>
                <hr>
                <div class="small text-muted">
                    Cada factura o nota de crédito se envía sola al emitirse. Los errores se reintentan cada hora
                    (con el programador de tareas activo en el servidor) o desde acá.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Últimos envíos</span>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('integracion-contable.index') }}" class="btn btn-outline-secondary {{ ! request('estado') ? 'active' : '' }}">Todos</a>
            <a href="{{ route('integracion-contable.index', ['estado' => 'error']) }}" class="btn btn-outline-danger {{ request('estado') === 'error' ? 'active' : '' }}">Con error</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Fecha</th><th>Documento</th><th>Estado</th><th>Detalle</th><th class="text-center">Intentos</th><th></th></tr></thead>
            <tbody>
            @forelse($registros as $r)
                <tr>
                    <td class="small text-nowrap">{{ $r->updated_at?->format('d/m/Y H:i') }}</td>
                    <td class="small">{{ \App\Models\SincronizacionContable::TIPOS[$r->tipo_origen] ?? $r->tipo_origen }} #{{ $r->id_externo }}</td>
                    <td>
                        @switch($r->estado)
                            @case('ok')<span class="badge text-bg-success">Enviado</span>@break
                            @case('error')<span class="badge text-bg-danger">Error</span>@break
                            @case('omitido')<span class="badge text-bg-secondary">Omitido</span>@break
                            @default<span class="badge text-bg-warning">Pendiente</span>
                        @endswitch
                    </td>
                    <td class="small {{ $r->estado === 'error' ? 'text-danger' : 'text-muted' }}">{{ $r->mensaje }}</td>
                    <td class="text-center small">{{ $r->intentos }}</td>
                    <td class="text-end">
                        @if($r->estado === 'error')
                            <form method="POST" action="{{ route('integracion-contable.reenviar', $r) }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-primary" title="Reenviar"><i class="bi bi-arrow-repeat"></i></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Todavía no se envió ningún documento.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($registros->hasPages())
        <div class="card-body">{{ $registros->links() }}</div>
    @endif
</div>
@endsection
