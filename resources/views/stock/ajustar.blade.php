@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-sliders me-2"></i>Ajustar Stock</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <p class="text-muted small">
            Ingresá la cantidad real que hay en el almacén. El sistema calcula la diferencia contra el stock actual
            y registra un movimiento de ajuste automáticamente.
        </p>

        <form action="{{ route('stock.guardarAjuste') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Producto *</label>
                    <select name="id_producto" class="form-select @error('id_producto') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id_producto }}" {{ old('id_producto') == $prod->id_producto ? 'selected' : '' }}>
                                {{ $prod->sku }} — {{ $prod->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_producto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Almacén *</label>
                    <select name="id_almacen" class="form-select @error('id_almacen') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($almacenes as $alm)
                            <option value="{{ $alm->id_almacen }}" {{ old('id_almacen') == $alm->id_almacen ? 'selected' : '' }}>
                                {{ $alm->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_almacen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Cantidad Real (nueva) *</label>
                    <input type="number" step="0.001" min="0" name="cantidad_nueva"
                           class="form-control @error('cantidad_nueva') is-invalid @enderror"
                           value="{{ old('cantidad_nueva') }}" required>
                    @error('cantidad_nueva') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Motivo *</label>
                    <input type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror"
                           value="{{ old('motivo') }}" placeholder="Conteo físico, rotura, vencimiento, corrección de carga..." required>
                    @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar Ajuste</button>
                <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection