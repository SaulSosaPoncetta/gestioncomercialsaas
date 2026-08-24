@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $listaPrecio->nombre ?? '') }}" placeholder="Minorista, Mayorista, Distribuidor..." required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">% Ganancia Base</label>
        <input type="number" step="0.01" min="0" name="porcentaje_ganancia_base"
               class="form-control @error('porcentaje_ganancia_base') is-invalid @enderror"
               value="{{ old('porcentaje_ganancia_base', $listaPrecio->porcentaje_ganancia_base ?? '') }}">
        @error('porcentaje_ganancia_base') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="es_predeterminada" name="es_predeterminada" value="1"
                {{ old('es_predeterminada', $listaPrecio->es_predeterminada ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="es_predeterminada">Lista predeterminada</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('listas-precios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>