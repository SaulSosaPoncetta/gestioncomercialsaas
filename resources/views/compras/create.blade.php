@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-cart-plus me-2"></i>Nueva Compra</h3>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form id="form-compra" action="{{ route('compras.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Proveedor *</label>
                    <select name="id_proveedor" class="form-select @error('id_proveedor') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id_persona }}" {{ old('id_proveedor') == $prov->id_persona ? 'selected' : '' }}>
                                {{ $prov->razon_social_nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_proveedor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sucursal *</label>
                    <select name="id_sucursal" class="form-select @error('id_sucursal') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->id_sucursal }}" {{ old('id_sucursal') == $suc->id_sucursal ? 'selected' : '' }}>
                                {{ $suc->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_sucursal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Almacén de destino *</label>
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

                <div class="col-md-3">
                    <label class="form-label">Tipo de Comprobante *</label>
                    <input type="text" name="tipo_comprobante" class="form-control @error('tipo_comprobante') is-invalid @enderror"
                           value="{{ old('tipo_comprobante', 'Factura Proveedor') }}" required>
                    @error('tipo_comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">N° Comprobante *</label>
                    <input type="text" name="numero_comprobante" class="form-control @error('numero_comprobante') is-invalid @enderror"
                           value="{{ old('numero_comprobante') }}" required>
                    @error('numero_comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Fecha de Emisión *</label>
                    <input type="date" name="fecha_emision" class="form-control @error('fecha_emision') is-invalid @enderror"
                           value="{{ old('fecha_emision', date('Y-m-d')) }}" required>
                    @error('fecha_emision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3">
                    <label class="form-label">Condición de Pago *</label>
                    <select name="condicion_pago" id="condicion_pago" class="form-select @error('condicion_pago') is-invalid @enderror" required>
                        <option value="CONTADO" {{ old('condicion_pago') == 'CONTADO' ? 'selected' : '' }}>Contado</option>
                        <option value="CREDITO" {{ old('condicion_pago') == 'CREDITO' ? 'selected' : '' }}>Crédito</option>
                    </select>
                    @error('condicion_pago') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3" id="campo-dias-vencimiento" style="display: none;">
                    <label class="form-label">Días para vencimiento</label>
                    <input type="number" name="dias_vencimiento" class="form-control" min="0" value="{{ old('dias_vencimiento', 30) }}">
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Productos</h5>

            <div class="row g-2 mb-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small">Producto</label>
                    <select id="selector-producto" class="form-select">
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id_producto }}"
                                    data-nombre="{{ $prod->nombre }}"
                                    data-sku="{{ $prod->sku }}"
                                    data-costo="{{ $prod->precio_costo }}">
                                {{ $prod->sku }} — {{ $prod->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Cantidad</label>
                    <input type="number" id="input-cantidad" class="form-control" step="0.001" min="0.001" value="1">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Costo Unitario</label>
                    <input type="number" id="input-costo" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-md-3">
                    <button type="button" id="btn-agregar-linea" class="btn btn-outline-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i> Agregar línea
                    </button>
                </div>
            </div>

            @error('productos')
            <div class="text-danger small mb-2">{{ $message }}</div>
            @enderror

            <div class="table-responsive mt-3">
                <table class="table table-sm align-middle" id="tabla-lineas">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Costo Unit.</th>
                            <th class="text-end">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo-lineas">
                        <tr id="fila-vacia">
                            <td colspan="6" class="text-center text-muted py-3">Todavía no agregaste productos.</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total</th>
                            <th class="text-end" id="total-compra">$0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div id="inputs-ocultos"></div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Registrar Compra</button>
                <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    let lineas = [];
    let contador = 0;

    const selectorProducto = document.getElementById('selector-producto');
    const inputCantidad = document.getElementById('input-cantidad');
    const inputCosto = document.getElementById('input-costo');
    const btnAgregar = document.getElementById('btn-agregar-linea');
    const cuerpoLineas = document.getElementById('cuerpo-lineas');
    const filaVacia = document.getElementById('fila-vacia');
    const totalCompraEl = document.getElementById('total-compra');
    const inputsOcultos = document.getElementById('inputs-ocultos');
    const selectCondicion = document.getElementById('condicion_pago');
    const campoDias = document.getElementById('campo-dias-vencimiento');

    selectorProducto.addEventListener('change', function () {
        const opt = this.selectedOptions[0];
        if (opt && opt.dataset.costo) {
            inputCosto.value = opt.dataset.costo;
        }
    });

    function toggleDiasVencimiento() {
        campoDias.style.display = selectCondicion.value === 'CREDITO' ? 'block' : 'none';
    }
    selectCondicion.addEventListener('change', toggleDiasVencimiento);
    toggleDiasVencimiento();

    btnAgregar.addEventListener('click', function () {
        const opt = selectorProducto.selectedOptions[0];
        if (!selectorProducto.value) {
            alert('Seleccioná un producto.');
            return;
        }
        const cantidad = parseFloat(inputCantidad.value);
        const costo = parseFloat(inputCosto.value);
        if (!cantidad || cantidad <= 0) {
            alert('Ingresá una cantidad válida.');
            return;
        }
        if (costo === null || isNaN(costo) || costo < 0) {
            alert('Ingresá un costo válido.');
            return;
        }

        contador++;
        lineas.push({
            id: contador,
            id_producto: selectorProducto.value,
            sku: opt.dataset.sku,
            nombre: opt.dataset.nombre,
            cantidad: cantidad,
            costo_unitario: costo,
        });

        selectorProducto.value = '';
        inputCantidad.value = 1;
        inputCosto.value = '';

        renderizar();
    });

    function eliminarLinea(id) {
        lineas = lineas.filter(l => l.id !== id);
        renderizar();
    }
    window.eliminarLinea = eliminarLinea;

    function renderizar() {
        cuerpoLineas.innerHTML = '';
        inputsOcultos.innerHTML = '';

        if (lineas.length === 0) {
            cuerpoLineas.appendChild(filaVacia);
            totalCompraEl.textContent = '$0.00';
            return;
        }

        let total = 0;

        lineas.forEach((linea, index) => {
            const subtotal = linea.cantidad * linea.costo_unitario;
            total += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${linea.sku}</td>
                <td>${linea.nombre}</td>
                <td class="text-end">${linea.cantidad}</td>
                <td class="text-end">$${linea.costo_unitario.toFixed(2)}</td>
                <td class="text-end">$${subtotal.toFixed(2)}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarLinea(${linea.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            cuerpoLineas.appendChild(tr);

            ['id_producto', 'cantidad', 'costo_unitario'].forEach(campo => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `productos[${index}][${campo}]`;
                input.value = linea[campo];
                inputsOcultos.appendChild(input);
            });
        });

        totalCompraEl.textContent = '$' + total.toFixed(2);
    }

    document.getElementById('form-compra').addEventListener('submit', function (e) {
        if (lineas.length === 0) {
            e.preventDefault();
            alert('Agregá al menos un producto a la compra.');
        }
    });
})();
</script>
@endsection