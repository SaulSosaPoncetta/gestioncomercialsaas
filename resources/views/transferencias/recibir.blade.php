@extends('layouts.app')

@section('content')
<h3 class="mb-4"><i class="bi bi-box-arrow-in-down me-2"></i>Recibir Transferencia — {{ $transferencia->numero_remito }}</h3>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="text-muted small">Desde</div>
                <div class="fw-semibold">{{ $transferencia->almacenOrigen->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Hacia</div>
                <div class="fw-semibold">{{ $transferencia->almacenDestino->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small">Fecha de Envío</div>
                <div class="fw-semibold">{{ $transferencia->fecha_envio->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <p class="text-muted small">
            Cotejá contra el remito físico que trajo el transporte. Si una cantidad recibida no coincide con lo enviado
            por el sistema, la fila se marca en rojo automáticamente — igual podés guardar así, y la transferencia quedará
            registrada como "Recibida con diferencia" para que quede visible.
        </p>

        <form action="{{ route('transferencias.guardarRecepcion', $transferencia) }}" method="POST">
            @csrf

            <div class="table-responsive mb-3">
                <table class="table align-middle" id="tabla-recepcion">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Producto</th>
                            <th class="text-end">Cantidad Enviada</th>
                            <th class="text-end" style="width: 160px;">Cantidad Recibida</th>
                            <th>Diferencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transferencia->detalles as $detalle)
                        <tr class="fila-detalle" data-enviado="{{ $detalle->cantidad_enviada }}">
                            <td>{{ $detalle->producto->sku ?? '—' }}</td>
                            <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                            <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad_enviada, 3, '.', ''), '0'), '.') }}</td>
                            <td class="text-end">
                                <input type="number" step="0.001" min="0"
                                       name="cantidades[{{ $detalle->id_detalle_transferencia }}]"
                                       class="form-control form-control-sm text-end input-cantidad-recibida"
                                       value="{{ $detalle->cantidad_enviada }}" required>
                            </td>
                            <td class="celda-diferencia text-muted">Sin diferencia</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mb-3">
                <label class="form-label">Observaciones de recepción (opcional)</label>
                <input type="text" name="observaciones_recepcion" class="form-control"
                       placeholder="Ej: faltó una caja del producto X, llegó con embalaje dañado...">
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Confirmar Recepción</button>
                <a href="{{ route('transferencias.pendientes') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    function actualizarFila(fila) {
        const enviado = parseFloat(fila.dataset.enviado);
        const input = fila.querySelector('.input-cantidad-recibida');
        const celda = fila.querySelector('.celda-diferencia');
        const recibido = parseFloat(input.value) || 0;
        const diferencia = recibido - enviado;

        if (diferencia === 0) {
            fila.classList.remove('table-danger');
            celda.className = 'celda-diferencia text-muted';
            celda.textContent = 'Sin diferencia';
        } else {
            fila.classList.add('table-danger');
            celda.className = 'celda-diferencia text-danger fw-semibold';
            celda.textContent = (diferencia > 0 ? '+' : '') + diferencia.toFixed(3) + (diferencia > 0 ? ' (sobrante)' : ' (faltante)');
        }
    }

    document.querySelectorAll('.fila-detalle').forEach(function (fila) {
        const input = fila.querySelector('.input-cantidad-recibida');
        input.addEventListener('input', () => actualizarFila(fila));
        actualizarFila(fila);
    });
})();
</script>
@endsection