@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Sucursales</h3>
    <a href="{{ route('sucursales.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Sucursal
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Código</th>
                    <th>Empresa</th>
                    <th>Casa Matriz</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sucursales as $sucursal)
                <tr>
                    <td>{{ $sucursal->nombre }}</td>
                    <td>{{ $sucursal->codigo }}</td>
                    <td>{{ $sucursal->empresa->razon_social ?? '-' }}</td>
                    <td>
                        @if($sucursal->es_casa_matriz)
                            <span class="badge bg-primary">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        @if($sucursal->estado)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-danger">Inactiva</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('sucursales.edit', $sucursal) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta sucursal?');">
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
                <tr><td colspan="6" class="text-center text-muted py-4">No hay sucursales cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $sucursales->links() }}
</div>
@endsection
