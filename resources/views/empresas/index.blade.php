@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-building-gear me-2"></i>Empresas</h3>
    <a href="{{ route('empresas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nueva Empresa
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Razón Social</th>
                    <th>Nombre Fantasía</th>
                    <th>CUIT / Identificación</th>
                    <th>Teléfono</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($empresas as $empresa)
                <tr>
                    <td>{{ $empresa->razon_social }}</td>
                    <td>{{ $empresa->nombre_fantasia }}</td>
                    <td>{{ $empresa->identificacion_fiscal }}</td>
                    <td>{{ $empresa->telefono }}</td>
                    <td class="text-end">
                        <a href="{{ route('empresas.edit', $empresa) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @can('eliminar-registros')
                        <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar esta empresa?');">
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
                <tr><td colspan="5" class="text-center text-muted py-4">No hay empresas cargadas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $empresas->links() }}
</div>
@endsection
