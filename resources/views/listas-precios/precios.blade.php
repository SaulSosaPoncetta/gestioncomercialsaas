@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0"><i class="bi bi-currency-dollar me-2"></i>Precios — {{ $listaPrecio->nombre }}</h3>
    <a href="{{ route('listas-precios.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('listas-precios.guardarPrecios', $listaPrecio) }}" method="POST">
            @csrf
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th class="text-end" style="width: 200px;">Precio de Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productos as $producto)
                        <tr>
                            <td>{{ $producto->sku }}</td>
                            <td>{{ $producto->nombre }}</td>
                            <td>
                                <input type="number" step="0.01" min="0"
                                       name="precios[{{ $producto->id_producto }}]"
                                       class="form-control text-end"
                                       value="{{ old('precios.'.$producto->id_producto, $preciosActuales[$producto->id_producto] ?? '') }}">
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-4">No hay productos activos cargados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($productos->isNotEmpty())
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar Precios</button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
