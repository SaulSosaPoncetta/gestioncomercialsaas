@csrf

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $marca->nombre ?? '') }}" required autofocus>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <div class="form-check form-switch mt-4">
            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1"
                {{ old('estado', $marca->estado ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="estado">Activa</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('marcas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>