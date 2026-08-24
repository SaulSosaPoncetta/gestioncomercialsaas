@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Movimientos</h3>
    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver a Stock
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('stock.historial') }}" class="row g-2">
            <div class="col-md-5">
                <select name="id_producto" class="form-select">
                    <option value="">Todos los productos</option>
                    @foreach($productos as $prod)
                        <option value="{{ $prod->id_producto }}" {{ request('id_producto') == $prod->id_producto ? 'selected' : '' }}>
                            {{ $prod->sku }} — {{ $prod->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                @if(request('id_producto'))
                <a href="{{ route('stock.historial') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th class="text-end">Cantidad</th>
                    <th>Almacén</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movimientos as $mov)
                <tr>
                    <td>{{ $mov->fecha->format('d/m/Y H:i') }}</td>
                    <td>{{ $mov->producto->nombre ?? '—' }}</td>
                    <td>
                        @php
                            $colorTipo = match($mov->tipo_movimiento) {
                                'ENTRADA_COMPRA' => 'success',
                                'SALIDA_VENTA' => 'danger',
                                'AJUSTE_POSITIVO' => 'primary',
                                'AJUSTE_NEGATIVO' => 'warning',
                                'TRANSFERENCIA' => 'info',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $colorTipo }}">{{ $mov->tipo_movimiento }}</span>
                    </td>
                    <td class="text-end">{{ rtrim(rtrim(number_format($mov->cantidad, 3, '.', ''), '0'), '.') }}</td>
                    <td>{{ $mov->almacenDestino->nombre ?? $mov->almacenOrigen->nombre ?? '—' }}</td>
                    <td>{{ $mov->motivo }}</td>
                    <td>{{ $mov->usuario->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay movimientos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $movimientos->appends(request()->query())->links() }}
</div>
@endsection