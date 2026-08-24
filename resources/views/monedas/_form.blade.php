@csrf

<div class="row g-3">
    <div class="col-md-3">
        <label class="form-label">Código *</label>
        <input type="text" name="codigo" class="form-control @error('codigo') is-invalid @enderror"
               value="{{ old('codigo', $moneda->codigo ?? '') }}" placeholder="ARS, USD, EUR..." maxlength="5" required>
        @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-5">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $moneda->nombre ?? '') }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Símbolo *</label>
        <input type="text" name="simbolo" class="form-control @error('simbolo') is-invalid @enderror"
               value="{{ old('simbolo', $moneda->simbolo ?? '') }}" placeholder="$, U$S, €..." maxlength="5" required>
        @error('simbolo') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Tipo de Cambio (respecto a la moneda base) *</label>
        <input type="number" step="0.0001" min="0" name="tipo_cambio"
               class="form-control @error('tipo_cambio') is-invalid @enderror"
               value="{{ old('tipo_cambio', $moneda->tipo_cambio ?? '1.0000') }}" required>
        @error('tipo_cambio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-4">
            <input type="checkbox" class="form-check-input" id="es_moneda_base" name="es_moneda_base" value="1"
                {{ old('es_moneda_base', $moneda->es_moneda_base ?? false) ? 'checked' : '' }}>
            <label class="form-check-label" for="es_moneda_base">Es la moneda base del sistema</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('monedas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>