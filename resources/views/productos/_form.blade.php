@csrf

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">SKU *</label>
        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
            value="{{ old('sku', $producto->sku ?? '') }}" required>
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Código de Barras</label>
        <input type="text" name="codigo_barras" class="form-control @error('codigo_barras') is-invalid @enderror"
            value="{{ old('codigo_barras', $producto->codigo_barras ?? '') }}">
        @error('codigo_barras')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $producto->nombre ?? '') }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="2">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Categoría *</label>
        <select name="id_categoria" class="form-select @error('id_categoria') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach ($categorias as $cat)
                <option value="{{ $cat->id_categoria }}"
                    {{ old('id_categoria', $producto->id_categoria ?? '') == $cat->id_categoria ? 'selected' : '' }}>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_categoria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Marca</label>
        <select name="id_marca" class="form-select @error('id_marca') is-invalid @enderror">
            <option value="">Sin marca</option>
            @foreach ($marcas as $marca)
                <option value="{{ $marca->id_marca }}"
                    {{ old('id_marca', $producto->id_marca ?? '') == $marca->id_marca ? 'selected' : '' }}>
                    {{ $marca->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_marca')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Impuesto *</label>
        <select name="id_impuesto" class="form-select @error('id_impuesto') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach ($impuestos as $imp)
                <option value="{{ $imp->id_impuesto }}"
                    {{ old('id_impuesto', $producto->id_impuesto ?? '') == $imp->id_impuesto ? 'selected' : '' }}>
                    {{ $imp->nombre }} ({{ $imp->porcentaje }}%)
                </option>
            @endforeach
        </select>
        @error('id_impuesto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Unidad de Medida *</label>
        <input type="text" name="unidad_medida" class="form-control @error('unidad_medida') is-invalid @enderror"
            value="{{ old('unidad_medida', $producto->unidad_medida ?? 'Unidad') }}" required>
        @error('unidad_medida')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @can('ver-costos')
        <div class="col-md-3">
            <label class="form-label">Precio de Costo *</label>
            <input type="number" step="0.01" min="0" name="precio_costo"
                class="form-control @error('precio_costo') is-invalid @enderror"
                value="{{ old('precio_costo', $producto->precio_costo ?? '0.00') }}" required>
            @error('precio_costo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @else
        <input type="hidden" name="precio_costo" value="{{ $producto->precio_costo ?? '0.00' }}">
    @endcan

    <div class="col-md-3">
        <label class="form-label">Stock Mínimo *</label>
        <input type="number" min="0" name="stock_minimo"
            class="form-control @error('stock_minimo') is-invalid @enderror"
            value="{{ old('stock_minimo', $producto->stock_minimo ?? '0') }}" required>
        @error('stock_minimo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Stock Máximo</label>
        <input type="number" min="0" name="stock_maximo"
            class="form-control @error('stock_maximo') is-invalid @enderror"
            value="{{ old('stock_maximo', $producto->stock_maximo ?? '') }}">
        @error('stock_maximo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="aplica_inventario" name="aplica_inventario"
                value="1" {{ old('aplica_inventario', $producto->aplica_inventario ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="aplica_inventario">Controla stock (desmarcar para servicios)</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1"
                {{ old('estado', $producto->estado ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="estado">Activo</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>
