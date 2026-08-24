@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-building me-2"></i>Almacenes</h3>
    <a href="{{ route('almacenes.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Almacén
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Sucursal</th>
                    <th>Ubicación</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($almacenes as $almacen)
                <tr>
                    <td>{{ $almacen->nombre }}</td>
                    <td>{{ $almacen->sucursal->nombre ?? '—' }}</td>
                    <td>{{ $almacen->ubicacion }}</td>
                    <td>
                        @if($almacen->estado)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('almacenes.edit', $almacen) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('almacenes.destroy', $almacen) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este almacén?');">
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
                <tr><td colspan="5" class="text-center text-muted py-4">No hay almacenes cargados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $almacenes->links() }}
</div>
@endsection
