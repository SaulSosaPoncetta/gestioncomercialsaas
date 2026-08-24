@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Cajas</h3>
    <a href="{{ route('cajas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Caja
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Sucursal</th>
                    <th>Estado de Sesión</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cajas as $caja)
                <tr>
                    <td>{{ $caja->nombre }}</td>
                    <td>{{ $caja->sucursal->nombre ?? '—' }}</td>
                    <td>
                        @if($caja->sesionAbierta)
                            <span class="badge bg-success"><i class="bi bi-unlock me-1"></i>Abierta</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Cerrada</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($caja->sesionAbierta)
                            <a href="{{ route('cajas.sesion', $caja->sesionAbierta) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-cash-coin"></i> Ver Sesión
                            </a>
                        @else
                            <a href="{{ route('cajas.abrir', $caja) }}" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-unlock"></i> Abrir
                            </a>
                        @endif
                        <a href="{{ route('cajas.edit', $caja) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('cajas.destroy', $caja) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta caja?');">
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
                <tr><td colspan="4" class="text-center text-muted py-4">No hay cajas cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $cajas->links() }}
</div>
@endsection
