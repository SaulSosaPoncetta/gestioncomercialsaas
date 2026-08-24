@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-arrow-left-right me-2"></i>Nueva Transferencia (Envío)</h3>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-1"></i>
    Al confirmar, se descuenta el stock del almacén de origen y se genera un remito para el transporte.
    El stock del destino se sumará recién cuando alguien confirme la <strong>recepción</strong> desde el otro lado.
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form id="form-transferencia" action="{{ route('transferencias.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label">Almacén de Origen *</label>
                    <select id="selector-origen" name="id_almacen_origen" class="form-select @error('id_almacen_origen') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($almacenes as $alm)
                            <option value="{{ $alm->id_almacen }}" {{ old('id_almacen_origen') == $alm->id_almacen ? 'selected' : '' }}>
                                {{ $alm->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_almacen_origen') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-2 d-flex align-items-center justify-content-center">
                    <i class="bi bi-arrow-right fs-3 text-muted mt-4"></i>
                </div>

                <div class="col-md-5">
                    <label class="form-label">Almacén de Destino *</label>
                    <select name="id_almacen_destino" class="form-select @error('id_almacen_destino') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($almacenes as $alm)
                            <option value="{{ $alm->id_almacen }}" {{ old('id_almacen_destino') == $alm->id_almacen ? 'selected' : '' }}>
                                {{ $alm->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_almacen_destino') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Motivo *</label>
                    <input type="text" name="motivo" class="form-control @error('motivo') is-invalid @enderror"
                           value="{{ old('motivo') }}" placeholder="Reposición de sucursal, redistribución de stock..." required>
                    @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Productos a enviar</h5>
            <p class="text-muted small" id="mensaje-sin-origen">Seleccioná un almacén de origen para ver su stock disponible.</p>

            <div class="table-responsive" id="tabla-stock-origen" style="display: none;">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th class="text-end">Disponible en Origen</th>
                            <th class="text-end" style="width: 140px;">Cantidad a Enviar</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo-stock-origen"></tbody>
                </table>
            </div>

            @error('productos')
            <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div id="inputs-ocultos"></div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-truck me-1"></i> Confirmar Envío y Generar Remito</button>
                <a href="{{ route('transferencias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const selectorOrigen = document.getElementById('selector-origen');
    const mensajeSinOrigen = document.getElementById('mensaje-sin-origen');
    const tablaStock = document.getElementById('tabla-stock-origen');
    const cuerpoStock = document.getElementById('cuerpo-stock-origen');
    const inputsOcultos = document.getElementById('inputs-ocultos');

    let itemsStock = [];

    selectorOrigen.addEventListener('change', async function () {
        if (!this.value) {
            tablaStock.style.display = 'none';
            mensajeSinOrigen.style.display = 'block';
            cuerpoStock.innerHTML = '';
            return;
        }

        const url = `{{ route('transferencias.stockPorAlmacen') }}?id_almacen=${this.value}`;
        const resp = await fetch(url);
        itemsStock = await resp.json();

        cuerpoStock.innerHTML = '';

        itemsStock.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.sku}</td>
                <td>${item.nombre}</td>
                <td class="text-end">${item.disponible}</td>
                <td class="text-end">
                    <input type="number" class="form-control form-control-sm text-end input-cantidad-transferir"
                           data-index="${index}" step="0.001" min="0" max="${item.disponible}" value="0">
                </td>
            `;
            cuerpoStock.appendChild(tr);
        });

        tablaStock.style.display = itemsStock.length ? 'block' : 'none';
        mensajeSinOrigen.style.display = itemsStock.length ? 'none' : 'block';
        if (!itemsStock.length) {
            mensajeSinOrigen.textContent = 'Este almacén no tiene stock disponible para transferir.';
        }

        actualizarInputsOcultos();
    });

    cuerpoStock.addEventListener('input', actualizarInputsOcultos);

    function actualizarInputsOcultos() {
        inputsOcultos.innerHTML = '';
        let contador = 0;

        document.querySelectorAll('.input-cantidad-transferir').forEach((input) => {
            const cantidad = parseFloat(input.value);
            if (!cantidad || cantidad <= 0) return;

            const index = parseInt(input.dataset.index);
            const item = itemsStock[index];

            const hiddenProducto = document.createElement('input');
            hiddenProducto.type = 'hidden';
            hiddenProducto.name = `productos[${contador}][id_producto]`;
            hiddenProducto.value = item.id_producto;
            inputsOcultos.appendChild(hiddenProducto);

            const hiddenCantidad = document.createElement('input');
            hiddenCantidad.type = 'hidden';
            hiddenCantidad.name = `productos[${contador}][cantidad]`;
            hiddenCantidad.value = cantidad;
            inputsOcultos.appendChild(hiddenCantidad);

            contador++;
        });
    }

    document.getElementById('form-transferencia').addEventListener('submit', function (e) {
        if (inputsOcultos.children.length === 0) {
            e.preventDefault();
            alert('Ingresá al menos una cantidad a enviar.');
        }
    });
})();
</script>
@endsection