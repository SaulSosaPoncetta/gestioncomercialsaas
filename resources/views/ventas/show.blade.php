@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-receipt-cutoff me-2"></i>Venta #{{ $venta->id_venta }}</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('ventas.remito.imprimir', $venta) }}" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-file-earmark-text me-1"></i> Remito
        </a>
        @if($venta->factura)
        <a href="{{ route('ventas.factura.imprimir', $venta) }}" target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-pdf me-1"></i> Factura
        </a>
        @endif
        <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver
        </a>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Cliente</div>
                <div class="fw-semibold">{{ $venta->cliente->razon_social_nombre ?? '—' }}</div>
                <div class="text-muted small mt-2">Vendedor</div>
                <div>{{ $venta->vendedor->name ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Fecha</div>
                <div class="fw-semibold">{{ $venta->fecha_venta->format('d/m/Y H:i') }}</div>
                <div class="text-muted small mt-2">Condición</div>
                <span class="badge bg-{{ $venta->tipo_venta == 'CONTADO' ? 'success' : 'warning' }}">
                    {{ $venta->tipo_venta }}
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total</div>
                <div class="fs-4 fw-bold">${{ number_format($venta->total_venta, 2) }}</div>
                @if($venta->cuentaPorCobrar)
                <div class="text-muted small mt-2">Saldo Pendiente</div>
                <div class="fw-semibold text-{{ $venta->cuentaPorCobrar->saldo_pendiente > 0 ? 'danger' : 'success' }}">
                    ${{ number_format($venta->cuentaPorCobrar->saldo_pendiente, 2) }}
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Factura</div>
                @if($venta->factura)
                    <div class="fw-semibold">{{ $venta->factura->numero_factura }}</div>
                    <div class="text-muted small">{{ $venta->factura->tipoComprobante->descripcion ?? '' }}</div>
                    <span class="badge bg-success mt-1">{{ $venta->factura->estado_fiscal }}</span>
                @else
                    <div class="text-muted">No se emitió factura</div>
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
                    <th class="text-end">Precio Unit.</th>
                    <th class="text-end">Descuento</th>
                    <th class="text-end">Impuesto</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->sku ?? '—' }}</td>
                    <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad, 3, '.', ''), '0'), '.') }}</td>
                    <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-end">${{ number_format($detalle->descuento, 2) }}</td>
                    <td class="text-end">${{ number_format($detalle->monto_impuesto, 2) }}</td>
                    <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
