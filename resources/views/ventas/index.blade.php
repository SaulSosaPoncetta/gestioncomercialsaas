@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-cart-check me-2"></i>Ventas</h3>
        <a href="{{ route('ventas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva Venta
        </a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('ventas.index') }}" class="d-flex gap-2">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por cliente..."
                    value="{{ request('buscar') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                @if (request('buscar'))
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
                <a href="{{ route('ventas.exportar', request()->query()) }}" class="btn btn-outline-success ms-auto">
                    <i class="bi bi-file-earmark-excel me-1"></i> Exportar a Excel
                </a>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Condición</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as $venta)
                        <tr>
                            <td>{{ $venta->fecha_venta->format('d/m/Y H:i') }}</td>
                            <td>{{ $venta->cliente->razon_social_nombre ?? '—' }}</td>
                            <td>{{ $venta->vendedor->name ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $venta->tipo_venta == 'CONTADO' ? 'success' : 'warning' }}">
                                    {{ $venta->tipo_venta }}
                                </span>
                            </td>
                            <td class="text-end">${{ number_format($venta->total_venta, 2) }}</td>
                            <td>
                                @php
                                    $colorEstado = match ($venta->estado) {
                                        'COMPLETADA' => 'success',
                                        'PRESUPUESTO' => 'secondary',
                                        'ANULADA' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $colorEstado }}">{{ $venta->estado }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $ventas->appends(request()->query())->links() }}
    </div>
@endsection
