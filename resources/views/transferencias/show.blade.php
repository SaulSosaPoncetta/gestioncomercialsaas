@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Transferencia {{ $transferencia->numero_remito }}</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('transferencias.remito.imprimir', $transferencia) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-text me-1"></i> Remito
        </a>
        @if($transferencia->estado === 'ENVIADA')
        <a href="{{ route('transferencias.recibir', $transferencia) }}" class="btn btn-success">
            <i class="bi bi-box-arrow-in-down me-1"></i> Recibir
        </a>
        @endif
        <a href="{{ route('transferencias.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Desde</div>
                <div class="fw-semibold">{{ $transferencia->almacenOrigen->nombre ?? '—' }}</div>
                <div class="text-muted small mt-2">Enviado por</div>
                <div>{{ $transferencia->usuarioEnvio->name ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Hacia</div>
                <div class="fw-semibold">{{ $transferencia->almacenDestino->nombre ?? '—' }}</div>
                <div class="text-muted small mt-2">Recibido por</div>
                <div>{{ $transferencia->usuarioRecepcion->name ?? '— Aún no recibido —' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Fecha Envío</div>
                <div class="fw-semibold">{{ $transferencia->fecha_envio->format('d/m/Y H:i') }}</div>
                <div class="text-muted small mt-2">Fecha Recepción</div>
                <div>{{ $transferencia->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Estado</div>
                @php
                    $color = match($transferencia->estado) {
                        'ENVIADA' => 'warning',
                        'RECIBIDA' => 'success',
                        'RECIBIDA_CON_DIFERENCIA' => 'danger',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge bg-{{ $color }} fs-6">{{ str_replace('_', ' ', $transferencia->estado) }}</span>
                @if($transferencia->observaciones_recepcion)
                <div class="text-muted small mt-2">{{ $transferencia->observaciones_recepcion }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th>Producto</th>
                    <th class="text-end">Enviado</th>
                    <th class="text-end">Recibido</th>
                    <th>Diferencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transferencia->detalles as $detalle)
                @php $diferencia = $detalle->diferencia(); @endphp
                <tr class="{{ $diferencia !== null && $diferencia != 0 ? 'table-danger' : '' }}">
                    <td>{{ $detalle->producto->sku ?? '—' }}</td>
                    <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad_enviada, 3, '.', ''), '0'), '.') }}</td>
                    <td class="text-end">
                        {{ $detalle->cantidad_recibida !== null ? rtrim(rtrim(number_format($detalle->cantidad_recibida, 3, '.', ''), '0'), '.') : '—' }}
                    </td>
                    <td>
                        @if($diferencia === null)
                            <span class="text-muted">Pendiente</span>
                        @elseif($diferencia == 0)
                            <span class="text-success">Sin diferencia</span>
                        @else
                            <span class="text-danger fw-semibold">
                                {{ $diferencia > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($diferencia, 3, '.', ''), '0'), '.') }}
                                {{ $diferencia > 0 ? '(sobrante)' : '(faltante)' }}
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection