@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-box-seam me-2"></i>Productos</h3>
        <a href="{{ route('productos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
        </a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('productos.index') }}" class="d-flex gap-2">
                <input type="text" name="buscar" class="form-control"
                    placeholder="Buscar por nombre, SKU o código de barras..." value="{{ request('buscar') }}">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                @if (request('buscar'))
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i
                            class="bi bi-x-lg"></i></a>
                @endif
                <a href="{{ route('productos.exportar', request()->query()) }}" class="btn btn-outline-success ms-auto">
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
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>Impuesto</th>
                        @can('ver-costos')
                            <th class="text-end">Costo</th>
                        @endcan
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productos as $producto)
                        <tr>
                            <td>{{ $producto->sku }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>{{ $producto->categoria->nombre ?? '—' }}</td>
                            <td>{{ $producto->marca->nombre ?? '—' }}</td>
                            <td>{{ $producto->impuesto->nombre ?? '—' }}</td>
                            @can('ver-costos')
                                <td class="text-end">${{ number_format($producto->precio_costo, 2) }}</td>
                            @endcan
                            <td>
                                @if ($producto->estado)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('productos.edit', $producto) }}"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @can('eliminar-registros')
                                    <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('¿Eliminar este producto?');">
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
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No hay productos cargados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $productos->links() }}
    </div>
@endsection
