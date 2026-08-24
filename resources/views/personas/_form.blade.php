@csrf

<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Tipo *</label>
        <select name="tipo_persona" class="form-select @error('tipo_persona') is-invalid @enderror" required>
            <option value="">Seleccionar...</option>
            @foreach(['CLIENTE' => 'Cliente', 'PROVEEDOR' => 'Proveedor', 'EMPLEADO' => 'Empleado', 'AMBOS' => 'Cliente y Proveedor'] as $valor => $label)
                <option value="{{ $valor }}" {{ old('tipo_persona', $persona->tipo_persona ?? '') == $valor ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('tipo_persona') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label">Nombre / Razón Social *</label>
        <input type="text" name="razon_social_nombre" class="form-control @error('razon_social_nombre') is-invalid @enderror"
               value="{{ old('razon_social_nombre', $persona->razon_social_nombre ?? '') }}" required>
        @error('razon_social_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Tipo de Documento *</label>
        <select name="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" required>
            @foreach(['DNI', 'CUIT', 'CUIL', 'RUC', 'PASAPORTE', 'OTRO'] as $tipo)
                <option value="{{ $tipo }}" {{ old('tipo_documento', $persona->tipo_documento ?? '') == $tipo ? 'selected' : '' }}>
                    {{ $tipo }}
                </option>
            @endforeach
        </select>
        @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Número de Documento *</label>
        <input type="text" name="numero_documento" class="form-control @error('numero_documento') is-invalid @enderror"
               value="{{ old('numero_documento', $persona->numero_documento ?? '') }}" required>
        @error('numero_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Condición IVA *</label>
        <input type="text" name="condicion_iva" class="form-control @error('condicion_iva') is-invalid @enderror"
               value="{{ old('condicion_iva', $persona->condicion_iva ?? 'Consumidor Final') }}" required>
        @error('condicion_iva') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $persona->telefono ?? '') }}">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $persona->email ?? '') }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Dirección</label>
        <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="2">{{ old('direccion', $persona->direccion ?? '') }}</textarea>
        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Límite de Crédito *</label>
        <input type="number" step="0.01" min="0" name="limite_credito"
               class="form-control @error('limite_credito') is-invalid @enderror"
               value="{{ old('limite_credito', $persona->limite_credito ?? '0.00') }}" required>
        @error('limite_credito') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Días de Crédito *</label>
        <input type="number" min="0" name="dias_credito"
               class="form-control @error('dias_credito') is-invalid @enderror"
               value="{{ old('dias_credito', $persona->dias_credito ?? '0') }}" required>
        @error('dias_credito') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <div class="form-check form-switch mt-4">
            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1"
                {{ old('estado', $persona->estado ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="estado">Activa</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('personas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>