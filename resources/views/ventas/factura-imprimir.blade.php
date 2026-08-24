<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $venta->factura->numero_factura }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #222; margin: 0; padding: 30px; }
        .encabezado { display: flex; justify-content: space-between; border-bottom: 3px solid #14532d; padding-bottom: 15px; margin-bottom: 20px; }
        .empresa h1 { margin: 0 0 4px; font-size: 20px; color: #14532d; }
        .empresa div { font-size: 12px; color: #555; }
        .comprobante { text-align: right; }
        .comprobante .tipo { font-size: 16px; font-weight: bold; border: 2px solid #14532d; padding: 6px 14px; display: inline-block; color: #14532d; }
        .comprobante .numero { font-size: 15px; margin-top: 6px; font-weight: bold; }
        .comprobante .fecha { font-size: 12px; color: #555; margin-top: 4px; }
        .datos-cliente { margin-bottom: 20px; border: 1px solid #ddd; padding: 12px 15px; border-radius: 4px; }
        .datos-cliente .fila { display: flex; justify-content: space-between; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #14532d; color: #fff; text-align: left; padding: 8px; font-size: 12px; }
        td { padding: 8px; border-bottom: 1px solid #eee; font-size: 12px; }
        .text-end { text-align: right; }
        .totales { width: 300px; margin-left: auto; }
        .totales .fila { display: flex; justify-content: space-between; padding: 4px 8px; }
        .totales .total-final { font-size: 16px; font-weight: bold; border-top: 2px solid #14532d; padding-top: 8px; margin-top: 4px; }
        .pie { margin-top: 40px; font-size: 11px; color: #888; text-align: center; }
        .btn-imprimir { position: fixed; top: 20px; right: 20px; background: #14532d; color: #fff; border: none; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        @media print {
            .btn-imprimir { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>

    <div class="encabezado">
        <div class="empresa">
            <h1>{{ $venta->sucursal->empresa->razon_social ?? config('app.name') }}</h1>
            <div>{{ $venta->sucursal->empresa->direccion ?? '' }}</div>
            <div>{{ $venta->sucursal->nombre ?? '' }}</div>
            <div>CUIT: {{ $venta->sucursal->empresa->identificacion_fiscal ?? '' }}</div>
        </div>
        <div class="comprobante">
            <div class="tipo">{{ $venta->factura->tipoComprobante->descripcion ?? 'Factura' }}</div>
            <div class="numero">N° {{ $venta->factura->numero_factura }}</div>
            <div class="fecha">Fecha: {{ $venta->factura->fecha_emision->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="datos-cliente">
        <div class="fila"><strong>Cliente:</strong><span>{{ $venta->cliente->razon_social_nombre ?? '—' }}</span></div>
        <div class="fila"><strong>{{ $venta->cliente->tipo_documento ?? 'Documento' }}:</strong><span>{{ $venta->cliente->numero_documento ?? '—' }}</span></div>
        <div class="fila"><strong>Condición IVA:</strong><span>{{ $venta->cliente->condicion_iva ?? '—' }}</span></div>
        <div class="fila"><strong>Dirección:</strong><span>{{ $venta->cliente->direccion ?? '—' }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Producto</th>
                <th class="text-end">Cantidad</th>
                <th class="text-end">Precio Unit.</th>
                <th class="text-end">Descuento</th>
                <th class="text-end">Impuesto</th>
                <th class="text-end">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->sku ?? '—' }}</td>
                <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad, 3, '.', ''), '0'), '.') }}</td>
                <td class="text-end">${{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-end">${{ number_format($detalle->descuento, 2) }}</td>
                <td class="text-end">${{ number_format($detalle->monto_impuesto, 2) }}</td>
                <td class="text-end">${{ number_format($detalle->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <div class="fila"><span>Subtotal</span><span>${{ number_format($venta->subtotal, 2) }}</span></div>
        <div class="fila"><span>Descuento</span><span>-${{ number_format($venta->descuento_total, 2) }}</span></div>
        <div class="fila"><span>Impuestos</span><span>${{ number_format($venta->impuesto_total, 2) }}</span></div>
        <div class="fila total-final"><span>TOTAL</span><span>${{ number_format($venta->total_venta, 2) }}</span></div>
    </div>

    <div class="pie">Documento generado por el sistema — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>