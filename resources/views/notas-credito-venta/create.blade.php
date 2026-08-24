@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-arrow-return-left me-2"></i>Nueva Nota de Crédito de Venta</h3>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form id="form-nota" action="{{ route('notas-credito-venta.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Venta a la que corresponde la devolución *</label>
                    <select id="selector-venta" name="id_venta" class="form-select @error('id_venta') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($ventas as $venta)
                            <option value="{{ $venta->id_venta }}" {{ old('id_venta') == $venta->id_venta ? 'selected' : '' }}>
                                Venta #{{ $venta->id_venta }} — {{ $venta->cliente->razon_social_nombre ?? 'Sin cliente' }} — {{ $venta->fecha_venta->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Almacén donde repone el stock *</label>
                    <select name="id_almacen" class="form-select @error('id_almacen') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($almacenes as $alm)
                            <option value="{{ $alm->id_almacen }}" {{ old('id_almacen') == $alm->id_almacen ? 'selected' : '' }}>
                                {{ $alm->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_almacen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Motivo *</label>
                    <input type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror"
                           value="{{ old('motivo') }}" placeholder="Producto defectuoso, cliente arrepentido, error de facturación..." required>
                    @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Productos a devolver</h5>
            <p class="text-muted small" id="mensaje-sin-venta">Seleccioná una venta arriba para ver sus productos.</p>

            <div class="table-responsive" id="tabla-productos-venta" style="display: none;">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th class="text-end">Vendido</th>
                            <th class="text-end">Disponible p/ Devolver</th>
                            <th class="text-end" style="width: 140px;">Cantidad a Devolver</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo-productos-venta"></tbody>
                </table>
            </div>

            @error('productos')
            <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div id="inputs-ocultos"></div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Emitir Nota de Crédito</button>
                <a href="{{ route('notas-credito-venta.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const selectorVenta = document.getElementById('selector-venta');
    const mensajeSinVenta = document.getElementById('mensaje-sin-venta');
    const tablaProductos = document.getElementById('tabla-productos-venta');
    const cuerpoProductos = document.getElementById('cuerpo-productos-venta');
    const inputsOcultos = document.getElementById('inputs-ocultos');

    let lineasDisponibles = [];

    selectorVenta.addEventListener('change', async function () {
        if (!this.value) {
            tablaProductos.style.display = 'none';
            mensajeSinVenta.style.display = 'block';
            cuerpoProductos.innerHTML = '';
            return;
        }

        const url = `{{ url('notas-credito-venta/datos-venta') }}/${this.value}`;
        const resp = await fetch(url);
        lineasDisponibles = await resp.json();

        cuerpoProductos.innerHTML = '';

        lineasDisponibles.forEach((linea, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${linea.sku}</td>
                <td>${linea.nombre}</td>
                <td class="text-end">${linea.cantidad_vendida}</td>
                <td class="text-end">${linea.cantidad_disponible}</td>
                <td class="text-end">
                    <input type="number" class="form-control form-control-sm text-end input-cantidad-devolver"
                           data-index="${index}" step="0.001" min="0" max="${linea.cantidad_disponible}" value="0"
                           ${linea.cantidad_disponible <= 0 ? 'disabled' : ''}>
                </td>
            `;
            cuerpoProductos.appendChild(tr);
        });

        tablaProductos.style.display = lineasDisponibles.length ? 'block' : 'none';
        mensajeSinVenta.style.display = lineasDisponibles.length ? 'none' : 'block';
        if (!lineasDisponibles.length) {
            mensajeSinVenta.textContent = 'Esta venta no tiene productos disponibles para devolver.';
        }

        actualizarInputsOcultos();
    });

    cuerpoProductos.addEventListener('input', actualizarInputsOcultos);

    function actualizarInputsOcultos() {
        inputsOcultos.innerHTML = '';
        let contador = 0;

        document.querySelectorAll('.input-cantidad-devolver').forEach((input) => {
            const cantidad = parseFloat(input.value);
            if (!cantidad || cantidad <= 0) return;

            const index = parseInt(input.dataset.index);
            const linea = lineasDisponibles[index];

            ['id_producto', 'precio_unitario'].forEach((campo) => {
                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = `productos[${contador}][${campo}]`;
                hidden.value = linea[campo];
                inputsOcultos.appendChild(hidden);
            });

            const hiddenCantidad = document.createElement('input');
            hiddenCantidad.type = 'hidden';
            hiddenCantidad.name = `productos[${contador}][cantidad]`;
            hiddenCantidad.value = cantidad;
            inputsOcultos.appendChild(hiddenCantidad);

            contador++;
        });
    }

    document.getElementById('form-nota').addEventListener('submit', function (e) {
        if (inputsOcultos.children.length === 0) {
            e.preventDefault();
            alert('Ingresá al menos una cantidad a devolver.');
        }
    });
})();
</script>
@endsection