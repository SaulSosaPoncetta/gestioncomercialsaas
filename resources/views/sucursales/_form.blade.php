@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Empresa *</label>
        <select name="id_empresa" class="form-select @error('id_empresa') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach($empresas as $emp)
                <option value="{{ $emp->id_empresa }}"
                    {{ old('id_empresa', $sucursal->id_empresa ?? '') == $emp->id_empresa ? 'selected' : '' }}>
                    {{ $emp->razon_social }}
                </option>
            @endforeach
        </select>
        @error('id_empresa') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $sucursal->nombre ?? '') }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Código *</label>
        <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
               value="{{ old('codigo', $sucursal->codigo ?? '') }}" required>
        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $sucursal->telefono ?? '') }}">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Dirección</label>
        <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="2">{{ old('direccion', $sucursal->direccion ?? '') }}</textarea>
        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="es_casa_matriz" name="es_casa_matriz" value="1"
                {{ old('es_casa_matriz', $sucursal->es_casa_matriz ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="es_casa_matriz">Es casa matriz</label>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1"
                {{ old('estado', $sucursal->estado ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="estado">Activa</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('sucursales.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>