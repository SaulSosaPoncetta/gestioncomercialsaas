@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-arrow-return-left me-2"></i>Nota de Crédito {{ $nota->numero_nota }}</h3>
    <a href="{{ route('notas-credito-venta.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Venta Original</div>
                <div class="fw-semibold"><a href="{{ route('ventas.show', $nota->id_venta) }}">Venta #{{ $nota->id_venta }}</a></div>
                <div class="text-muted small mt-2">Cliente</div>
                <div>{{ $nota->venta->cliente->razon_social_nombre ?? '—' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Fecha</div>
                <div class="fw-semibold">{{ $nota->fecha->format('d/m/Y') }}</div>
                <div class="text-muted small mt-2">Motivo</div>
                <div>{{ $nota->motivo }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small">Total</div>
                <div class="fs-4 fw-bold">${{ number_format($nota->total, 2) }}</div>
                <span class="badge bg-{{ $nota->estado == 'EMITIDA' ? 'success' : 'danger' }} mt-1">{{ $nota->estado }}</span>
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
                    <th class="text-end">Impuesto</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nota->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->producto->sku ?? '—' }}</td>
                    <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad, 3, '.', ''), '0'), '.') }}</td>
                    <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                    <td class="text-end">${{ number_format($detalle->monto_impuesto, 2) }}</td>
                    <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection