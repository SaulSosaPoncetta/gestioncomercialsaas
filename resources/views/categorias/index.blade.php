@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Categorías</h3>
    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Categoría Padre</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $categoria)
                <tr>
                    <td>{{ $categoria->nombre }}</td>
                    <td>{{ $categoria->padre->nombre ?? '—' }}</td>
                    <td>
                        @if($categoria->estado)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-danger">Inactiva</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta categoría?');">
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
                <tr><td colspan="4" class="text-center text-muted py-4">No hay categorías cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $categorias->links() }}
</div>
@endsection
