@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Sucursal *</label>
        <select name="id_sucursal" class="form-select @error('id_sucursal') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach($sucursales as $suc)
                <option value="{{ $suc->id_sucursal }}"
                    {{ old('id_sucursal', $almacen->id_sucursal ?? '') == $suc->id_sucursal ? 'selected' : '' }}>
                    {{ $suc->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_sucursal') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $almacen->nombre ?? '') }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Ubicación</label>
        <textarea name="ubicacion" class="form-control @error('ubicacion') is-invalid @enderror" rows="2">{{ old('ubicacion', $almacen->ubicacion ?? '') }}</textarea>
        @error('ubicacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1"
                {{ old('estado', $almacen->estado ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="estado">Activo</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('almacenes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>