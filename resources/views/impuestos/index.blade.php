@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-percent me-2"></i>Impuestos</h3>
    <a href="{{ route('impuestos.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Impuesto
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Porcentaje</th>
                    <th>Predeterminado</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($impuestos as $impuesto)
                <tr>
                    <td>{{ $impuesto->nombre }}</td>
                    <td>{{ number_format($impuesto->porcentaje, 2) }}%</td>
                    <td>
                        @if($impuesto->es_predeterminado)
                            <span class="badge bg-primary">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        @if($impuesto->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('impuestos.edit', $impuesto) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('impuestos.destroy', $impuesto) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este impuesto?');">
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
                <tr><td colspan="5" class="text-center text-muted py-4">No hay impuestos cargados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $impuestos->links() }}
</div>
@endsection
