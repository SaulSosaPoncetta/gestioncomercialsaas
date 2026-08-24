@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Razón Social *</label>
        <input type="text" name="razon_social" class="form-control @error('razon_social') is-invalid @enderror"
               value="{{ old('razon_social', $empresa->razon_social ?? '') }}" required>
        @error('razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Nombre Fantasía</label>
        <input type="text" name="nombre_fantasia" class="form-control @error('nombre_fantasia') is-invalid @enderror"
               value="{{ old('nombre_fantasia', $empresa->nombre_fantasia ?? '') }}">
        @error('nombre_fantasia') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">CUIT / Identificación Fiscal *</label>
        <input type="text" name="identificacion_fiscal" class="form-control @error('identificacion_fiscal') is-invalid @enderror"
               value="{{ old('identificacion_fiscal', $empresa->identificacion_fiscal ?? '') }}" required>
        @error('identificacion_fiscal') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
               value="{{ old('telefono', $empresa->telefono ?? '') }}">
        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $empresa->email ?? '') }}">
        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Dirección</label>
        <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="2">{{ old('direccion', $empresa->direccion ?? '') }}</textarea>
        @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('empresas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>