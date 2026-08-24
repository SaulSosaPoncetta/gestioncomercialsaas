@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-cart-check me-2"></i>Nueva Venta</h3>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form id="form-venta" action="{{ route('ventas.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Cliente *</label>
                    <select name="id_cliente" class="form-select @error('id_cliente') is-invalid @enderror" required>
                        <option value="">Seleccionar...</option>
                        @foreach($clientes as $cli)
                            <option value="{{ $cli->id_persona }}" {{ old('id_cliente') == $cli->id_persona ? 'selected' : '' }}>
                                {{ $cli->razon_social_nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_cliente') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <label class="form-label">Almacén de origen *</label>
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
                    <label class="form-label">Lista de Precios</label>
                    <select id="selector-lista" class="form-select">
                        <option value="">Precio manual</option>
                        @foreach($listasPrecios as $lista)
                            <option value="{{ $lista->id_lista_precio }}">{{ $lista->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Condición de Venta *</label>
                    <select name="tipo_venta" id="tipo_venta" class="form-select @error('tipo_venta') is-invalid @enderror" required>
                        <option value="CONTADO" {{ old('tipo_venta') == 'CONTADO' ? 'selected' : '' }}>Contado</option>
                        <option value="CREDITO" {{ old('tipo_venta') == 'CREDITO' ? 'selected' : '' }}>Crédito</option>
                    </select>
                    @error('tipo_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-3" id="campo-dias-vencimiento" style="display: none;">
                    <label class="form-label">Días para vencimiento</label>
                    <input type="number" name="dias_vencimiento" class="form-control" min="0" value="{{ old('dias_vencimiento', 30) }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="emitir_factura" name="emitir_factura" value="1"
                            {{ old('emitir_factura') ? 'checked' : '' }}>
                        <label class="form-check-label" for="emitir_factura">Emitir factura</label>
                    </div>
                </div>

                <div class="col-md-6" id="campo-tipo-comprobante" style="display: none;">
                    <label class="form-label">Tipo de Comprobante</label>
                    <select name="id_tipo_comprobante" class="form-select">
                        <option value="">Seleccionar...</option>
                        @foreach(\App\Models\TipoComprobante::orderBy('descripcion')->get() as $tc)
                            <option value="{{ $tc->id_tipo_comprobante }}">{{ $tc->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Productos</h5>

            <div class="row g-2 mb-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small">Producto</label>
                    <select id="selector-producto" class="form-select">
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id_producto }}"
                                    data-nombre="{{ $prod->nombre }}"
                                    data-sku="{{ $prod->sku }}"
                                    data-precio="{{ $prod->precio_costo }}"
                                    data-impuesto="{{ $prod->impuesto->porcentaje ?? 0 }}">
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
                    <label class="form-label small">Precio Unitario</label>
                    <input type="number" id="input-precio" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Descuento</label>
                    <input type="number" id="input-descuento" class="form-control" step="0.01" min="0" value="0">
                </div>
                <div class="col-md-2">
                    <button type="button" id="btn-agregar-linea" class="btn btn-outline-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i> Agregar
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
                            <th class="text-end">Cant.</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end">Desc.</th>
                            <th class="text-end">Impuesto</th>
                            <th class="text-end">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cuerpo-lineas">
                        <tr id="fila-vacia">
                            <td colspan="8" class="text-center text-muted py-3">Todavía no agregaste productos.</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-end">Total</th>
                            <th class="text-end" id="total-venta">$0.00</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div id="inputs-ocultos"></div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Registrar Venta</button>
                <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    let lineas = [];
    let contador = 0;
    let preciosLista = {};

    const selectorLista = document.getElementById('selector-lista');
    const selectorProducto = document.getElementById('selector-producto');
    const inputCantidad = document.getElementById('input-cantidad');
    const inputPrecio = document.getElementById('input-precio');
    const inputDescuento = document.getElementById('input-descuento');
    const btnAgregar = document.getElementById('btn-agregar-linea');
    const cuerpoLineas = document.getElementById('cuerpo-lineas');
    const filaVacia = document.getElementById('fila-vacia');
    const totalVentaEl = document.getElementById('total-venta');
    const inputsOcultos = document.getElementById('inputs-ocultos');
    const selectTipoVenta = document.getElementById('tipo_venta');
    const campoDias = document.getElementById('campo-dias-vencimiento');
    const checkFactura = document.getElementById('emitir_factura');
    const campoTipoComprobante = document.getElementById('campo-tipo-comprobante');

    function toggleDiasVencimiento() {
        campoDias.style.display = selectTipoVenta.value === 'CREDITO' ? 'block' : 'none';
    }
    selectTipoVenta.addEventListener('change', toggleDiasVencimiento);
    toggleDiasVencimiento();

    function toggleTipoComprobante() {
        campoTipoComprobante.style.display = checkFactura.checked ? 'block' : 'none';
    }
    checkFactura.addEventListener('change', toggleTipoComprobante);
    toggleTipoComprobante();

    selectorLista.addEventListener('change', async function () {
        preciosLista = {};
        if (!this.value) return;

        try {
            const resp = await fetch(`{{ route('ventas.preciosPorLista') }}?id_lista_precio=${this.value}`);
            preciosLista = await resp.json();
        } catch (e) {
            preciosLista = {};
        }

        actualizarPrecioSugerido();
    });

    function actualizarPrecioSugerido() {
        const opt = selectorProducto.selectedOptions[0];
        if (!opt || !opt.value) return;

        if (preciosLista[opt.value] !== undefined) {
            inputPrecio.value = preciosLista[opt.value];
        } else {
            inputPrecio.value = opt.dataset.precio || '';
        }
    }

    selectorProducto.addEventListener('change', actualizarPrecioSugerido);

    btnAgregar.addEventListener('click', function () {
        const opt = selectorProducto.selectedOptions[0];
        if (!selectorProducto.value) {
            alert('Seleccioná un producto.');
            return;
        }
        const cantidad = parseFloat(inputCantidad.value);
        const precio = parseFloat(inputPrecio.value);
        const descuento = parseFloat(inputDescuento.value) || 0;

        if (!cantidad || cantidad <= 0) {
            alert('Ingresá una cantidad válida.');
            return;
        }
        if (precio === null || isNaN(precio) || precio < 0) {
            alert('Ingresá un precio válido.');
            return;
        }

        contador++;
        lineas.push({
            id: contador,
            id_producto: selectorProducto.value,
            sku: opt.dataset.sku,
            nombre: opt.dataset.nombre,
            cantidad: cantidad,
            precio_unitario: precio,
            descuento: descuento,
            porcentaje_impuesto: parseFloat(opt.dataset.impuesto) || 0,
        });

        selectorProducto.value = '';
        inputCantidad.value = 1;
        inputPrecio.value = '';
        inputDescuento.value = 0;

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
            totalVentaEl.textContent = '$0.00';
            return;
        }

        let total = 0;

        lineas.forEach((linea, index) => {
            const baseLinea = (linea.cantidad * linea.precio_unitario) - linea.descuento;
            const impuestoLinea = baseLinea * (linea.porcentaje_impuesto / 100);
            const subtotal = baseLinea + impuestoLinea;
            total += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${linea.sku}</td>
                <td>${linea.nombre}</td>
                <td class="text-end">${linea.cantidad}</td>
                <td class="text-end">$${linea.precio_unitario.toFixed(2)}</td>
                <td class="text-end">$${linea.descuento.toFixed(2)}</td>
                <td class="text-end">$${impuestoLinea.toFixed(2)}</td>
                <td class="text-end">$${subtotal.toFixed(2)}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarLinea(${linea.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            cuerpoLineas.appendChild(tr);

            ['id_producto', 'cantidad', 'precio_unitario', 'descuento'].forEach(campo => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `productos[${index}][${campo}]`;
                input.value = linea[campo];
                inputsOcultos.appendChild(input);
            });
        });

        totalVentaEl.textContent = '$' + total.toFixed(2);
    }

    document.getElementById('form-venta').addEventListener('submit', function (e) {
        if (lineas.length === 0) {
            e.preventDefault();
            alert('Agregá al menos un producto a la venta.');
        }
    });
})();
</script>
@endsection