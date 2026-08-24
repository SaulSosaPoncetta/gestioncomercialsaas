@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-receipt me-2"></i>Compra {{ $compra->tipo_comprobante }} {{ $compra->numero_comprobante }}</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('compras.remito.imprimir', $compra) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-text me-1"></i> Remito
        </a>
        <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Proveedor</div>
                <div class="fw-semibold">{{ $compra->proveedor->razon_social_nombre ?? '—' }}</div>
                <div class="text-muted small mt-2">Sucursal</div>
                <div>{{ $compra->sucursal->nombre ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Fecha de Emisión</div>
                <div class="fw-semibold">{{ $compra->fecha_emision->format('d/m/Y') }}</div>
                <div class="text-muted small mt-2">Condición de Pago</div>
                <span class="badge bg-{{ $compra->condicion_pago == 'CONTADO' ? 'success' : 'warning' }}">
                    {{ $compra->condicion_pago }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total</div>
                <div class="fs-4 fw-bold">${{ number_format($compra->total_compra, 2) }}</div>
                @if($compra->cuentaPorPagar)
                <div class="text-muted small mt-2">Saldo Pendiente</div>
                <div class="fw-semibold text-{{ $compra->cuentaPorPagar->saldo_pendiente > 0 ? 'danger' : 'success' }}">
                    ${{ number_format($compra->cuentaPorPagar->saldo_pendiente, 2) }}
                </div>
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
                    <th class="text-end">Cantidad</th>
                    <th class="text-end">Costo Unit.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($compra->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->sku ?? '—' }}</td>
                    <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad, 3, '.', ''), '0'), '.') }}</td>
                    <td class="text-end">${{ number_format($detalle->costo_unitario, 2) }}</td>
                    <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
