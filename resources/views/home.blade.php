@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h3>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0" style="border-left: 4px solid #14532d !important;">
            <div class="card-body">
                <div class="text-muted small">Ventas de Hoy</div>
                <div class="fs-3 fw-bold">${{ number_format($totalVentasHoy, 2) }}</div>
                <div class="text-muted small">{{ $cantidadVentasHoy }} venta{{ $cantidadVentasHoy == 1 ? '' : 's' }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0" style="border-left: 4px solid #dc3545 !important;">
            <div class="card-body">
                <div class="text-muted small">Productos con Stock Bajo</div>
                <div class="fs-3 fw-bold">{{ $stockBajo->count() }}</div>
                <div class="text-muted small">requieren reposición</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-0" style="border-left: 4px solid #ffc107 !important;">
            <div class="card-body">
                <div class="text-muted small">Cuentas por Vencer</div>
                <div class="fs-3 fw-bold">{{ $cxcPorVencer->count() + $cxpPorVencer->count() }}</div>
                <div class="text-muted small">a cobrar y a pagar</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-exclamation-triangle text-danger me-1"></i> Stock Bajo
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th class="text-end">Actual</th>
                            <th class="text-end">Mínimo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockBajo as $item)
                        <tr>
                            <td>{{ $item->producto->nombre ?? '—' }}</td>
                            <td>{{ $item->almacen->nombre ?? '—' }}</td>
                            <td class="text-end text-danger fw-semibold">{{ rtrim(rtrim(number_format($item->cantidad, 3, '.', ''), '0'), '.') }}</td>
                            <td class="text-end">{{ $item->producto->stock_minimo ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Todo el stock está por encima del mínimo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-calendar-event text-warning me-1"></i> Cuentas por Vencer
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo</th>
                            <th>Persona</th>
                            <th>Vence</th>
                            <th class="text-end">Saldo</th>
                        </tr>
                    </thead>
                                        <tbody>
                        @foreach($cxcPorVencer as $cxc)
                        <tr>
                            <td><span class="badge bg-info">Cobrar</span></td>
                            <td>{{ $cxc->cliente->razon_social_nombre ?? '—' }}</td>
                            <td>{{ $cxc->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td class="text-end">${{ number_format($cxc->saldo_pendiente, 2) }}</td>
                        </tr>
                        @endforeach
                        @foreach($cxpPorVencer as $cxp)
                        <tr>
                            <td><span class="badge bg-secondary">Pagar</span></td>
                            <td>{{ $cxp->proveedor->razon_social_nombre ?? '—' }}</td>
                            <td>{{ $cxp->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td class="text-end">${{ number_format($cxp->saldo_pendiente, 2) }}</td>
                        </tr>
                        @endforeach
                        @if($cxcPorVencer->isEmpty() && $cxpPorVencer->isEmpty())
                        <tr><td colspan="4" class="text-center text-muted py-3">No hay cuentas pendientes.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-graph-up-arrow text-success me-1"></i> Más Vendidos
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Cantidad Vendida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($masVendidos as $item)
                        <tr>
                            <td>{{ $item->producto->nombre ?? '—' }}</td>
                            <td class="text-end fw-semibold">{{ rtrim(rtrim(number_format($item->total_cantidad, 3, '.', ''), '0'), '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted py-3">Todavía no hay ventas registradas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="bi bi-graph-down-arrow text-danger me-1"></i> Menos Vendidos (desde su compra)
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-end">Cantidad Vendida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menosVendidos as $prod)
                        <tr>
                            <td>{{ $prod->nombre }}</td>
                            <td class="text-end fw-semibold">{{ rtrim(rtrim(number_format($prod->total_vendido ?? 0, 3, '.', ''), '0'), '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted py-3">Todavía no hay productos comprados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection