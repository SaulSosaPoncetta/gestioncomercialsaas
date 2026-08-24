@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-people me-2"></i>Personas</h3>
    <a href="{{ route('personas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Persona
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('personas.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre o documento..."
                       value="{{ request('buscar') }}">
            </div>
            <div class="col-md-3">
                <select name="tipo" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="CLIENTE" {{ request('tipo') == 'CLIENTE' ? 'selected' : '' }}>Cliente</option>
                    <option value="PROVEEDOR" {{ request('tipo') == 'PROVEEDOR' ? 'selected' : '' }}>Proveedor</option>
                    <option value="EMPLEADO" {{ request('tipo') == 'EMPLEADO' ? 'selected' : '' }}>Empleado</option>
                    <option value="AMBOS" {{ request('tipo') == 'AMBOS' ? 'selected' : '' }}>Cliente y Proveedor</option>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                @if(request('buscar') || request('tipo'))
                <a href="{{ route('personas.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre / Razón Social</th>
                    <th>Tipo</th>
                    <th>Documento</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($personas as $persona)
                <tr>
                    <td>{{ $persona->razon_social_nombre }}</td>
                    <td>
                        @php
                            $badgeColor = match($persona->tipo_persona) {
                                'CLIENTE' => 'info',
                                'PROVEEDOR' => 'warning',
                                'EMPLEADO' => 'secondary',
                                'AMBOS' => 'primary',
                                default => 'secondary',
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeColor }}">{{ $persona->tipo_persona }}</span>
                    </td>
                    <td>{{ $persona->tipo_documento }}: {{ $persona->numero_documento }}</td>
                    <td>{{ $persona->telefono }}</td>
                    <td>
                        @if($persona->estado)
                            <span class="badge bg-success">Activa</span>
                        @else
                            <span class="badge bg-danger">Inactiva</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('personas.edit', $persona) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('personas.destroy', $persona) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta persona?');">
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
                <tr><td colspan="6" class="text-center text-muted py-4">No hay personas cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $personas->appends(request()->query())->links() }}
</div>
@endsection
