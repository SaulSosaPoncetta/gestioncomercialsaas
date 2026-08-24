@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Listas de Precios</h3>
    <a href="{{ route('listas-precios.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Lista
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>% Ganancia Base</th>
                    <th>Predeterminada</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listasPrecios as $lista)
                <tr>
                    <td>{{ $lista->nombre }}</td>
                    <td>{{ $lista->porcentaje_ganancia_base !== null ? number_format($lista->porcentaje_ganancia_base, 2).'%' : '—' }}</td>
                    <td>
                        @if($lista->es_predeterminada)
                            <span class="badge bg-primary">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('listas-precios.precios', $lista) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-currency-dollar"></i> Precios
                        </a>
                        <a href="{{ route('listas-precios.edit', $lista) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('listas-precios.destroy', $lista) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta lista de precios?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No hay listas de precios cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $listasPrecios->links() }}
</div>
@endsection
