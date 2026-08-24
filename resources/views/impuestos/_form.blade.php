@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $impuesto->nombre ?? '') }}" placeholder="IVA General, IVA Reducido, Exento..." required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Porcentaje (%) *</label>
        <input type="number" step="0.01" min="0" max="100" name="porcentaje"
               class="form-control @error('porcentaje') is-invalid @enderror"
               value="{{ old('porcentaje', $impuesto->porcentaje ?? '0.00') }}" required>
        @error('porcentaje') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="es_predeterminado" name="es_predeterminado" value="1"
                {{ old('es_predeterminado', $impuesto->es_predeterminado ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="es_predeterminado">Predeterminado (se usa por defecto en productos nuevos)</label>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1"
                {{ old('estado', $impuesto->estado ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="estado">Activo</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('impuestos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>