@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre *</label>
        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
               value="{{ old('nombre', $categoria->nombre ?? '') }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Categoría Padre (opcional)</label>
        <select name="id_categoria_padre" class="form-select @error('id_categoria_padre') is-invalid @enderror">
            <option value="">— Ninguna (categoría raíz) —</option>
            @foreach($categoriasPadre as $cat)
                <option value="{{ $cat->id_categoria }}"
                    {{ old('id_categoria_padre', $categoria->id_categoria_padre ?? '') == $cat->id_categoria ? 'selected' : '' }}>
                    {{ $cat->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_categoria_padre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" class="form-check-input" id="estado" name="estado" value="1"
                {{ old('estado', $categoria->estado ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="estado">Activa</label>
        </div>
    </div>
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Guardar</button>
    <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
</div>