@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-boxes me-2"></i>Stock</h3>
        <div class="d-flex gap-2">
            <a href="{{ route('stock.historial') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-1"></i> Historial
            </a>
            <a href="{{ route('stock.ajustar') }}" class="btn btn-primary">
                <i class="bi bi-sliders me-1"></i> Ajustar Stock
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('stock.index') }}" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o SKU..."
                        value="{{ request('buscar') }}">
                </div>
                <div class="col-md-4">
                    <select name="id_almacen" class="form-select">
                        <option value="">Todos los almacenes</option>
                        @foreach ($almacenes as $alm)
                            <option value="{{ $alm->id_almacen }}"
                                {{ request('id_almacen') == $alm->id_almacen ? 'selected' : '' }}>
                                {{ $alm->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-auto">
                    <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                    @if (request('buscar') || request('id_almacen'))
                        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary"><i
                                class="bi bi-x-lg"></i></a>
                    @endif
                </div>
                <div class="col-md-auto ms-auto">
                    <a href="{{ route('stock.exportar', request()->query()) }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel
                    </a>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Stock Mínimo</th>
                        <th>Alerta</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stock as $item)
                        <tr>
                            <td>{{ $item->producto->sku ?? '—' }}</td>
                            <td>{{ $item->producto->nombre ?? '—' }}</td>
                            <td>{{ $item->almacen->nombre ?? '—' }}</td>
                            <td class="text-end">{{ rtrim(rtrim(number_format($item->cantidad, 3, '.', ''), '0'), '.') }}
                            </td>
                            <td class="text-end">{{ $item->producto->stock_minimo ?? 0 }}</td>
                            <td>
                                @if ($item->producto && $item->cantidad <= $item->producto->stock_minimo)
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Bajo
                                        mínimo</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay stock cargado todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $stock->appends(request()->query())->links() }}
    </div>
@endsection
