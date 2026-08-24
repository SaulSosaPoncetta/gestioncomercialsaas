@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-arrow-return-right me-2"></i>Notas de Crédito de Compra</h3>
    <a href="{{ route('notas-credito-compra.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Nota de Crédito
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('notas-credito-compra.index') }}" class="d-flex gap-2">
            <input type="text" name="buscar" class="form-control" placeholder="Buscar por número de nota..."
                   value="{{ request('buscar') }}">
            <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
            @if(request('buscar'))
            <a href="{{ route('notas-credito-compra.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Número</th>
                    <th>Fecha</th>
                    <th>Compra Original</th>
                    <th>Proveedor</th>
                    <th class="text-end">Total</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($notas as $nota)
                <tr>
                    <td>{{ $nota->numero_nota }}</td>
                    <td>{{ $nota->fecha->format('d/m/Y') }}</td>
                    <td><a href="{{ route('compras.show', $nota->id_compra) }}">Compra #{{ $nota->id_compra }}</a></td>
                    <td>{{ $nota->compra->proveedor->razon_social_nombre ?? '—' }}</td>
                    <td class="text-end">${{ number_format($nota->total, 2) }}</td>
                    <td>
                        <span class="badge bg-{{ $nota->estado == 'EMITIDA' ? 'success' : 'danger' }}">{{ $nota->estado }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('notas-credito-compra.show', $nota) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay notas de crédito registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $notas->appends(request()->query())->links() }}
</div>
@endsection