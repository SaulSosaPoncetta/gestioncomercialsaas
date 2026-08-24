@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Compras</h3>
        <a href="{{ route('compras.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nueva Compra
        </a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('compras.index') }}" class="d-flex gap-2">
                <input type="text" name="buscar" class="form-control"
                    placeholder="Buscar por N° comprobante o proveedor..." value="{{ request('buscar') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                @if (request('buscar'))
                    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
                <a href="{{ route('compras.exportar', request()->query()) }}" class="btn btn-outline-success ms-auto">
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
                        <th>Comprobante</th>
                        <th>Proveedor</th>
                        <th>Sucursal</th>
                        <th>Condición</th>
                        <th class="text-end">Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                        <tr>
                            <td>{{ $compra->fecha_emision->format('d/m/Y') }}</td>
                            <td>{{ $compra->tipo_comprobante }} {{ $compra->numero_comprobante }}</td>
                            <td>{{ $compra->proveedor->razon_social_nombre ?? '—' }}</td>
                            <td>{{ $compra->sucursal->nombre ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $compra->condicion_pago == 'CONTADO' ? 'success' : 'warning' }}">
                                    {{ $compra->condicion_pago }}
                                </span>
                            </td>
                            <td class="text-end">${{ number_format($compra->total_compra, 2) }}</td>
                            <td>
                                @php
                                    $colorEstado = match ($compra->estado) {
                                        'RECIBIDO' => 'success',
                                        'PENDIENTE' => 'warning',
                                        'ANULADO' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $colorEstado }}">{{ $compra->estado }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('compras.show', $compra) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay compras registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $compras->appends(request()->query())->links() }}
    </div>
@endsection
