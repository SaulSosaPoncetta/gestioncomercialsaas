<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Remito Recepción — Compra #{{ $compra->id_compra }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #222; margin: 0; padding: 30px; }
        .encabezado { display: flex; justify-content: space-between; border-bottom: 3px solid #14532d; padding-bottom: 15px; margin-bottom: 20px; }
        .empresa h1 { margin: 0 0 4px; font-size: 20px; color: #14532d; }
        .empresa div { font-size: 12px; color: #555; }
        .comprobante { text-align: right; }
        .comprobante .tipo { font-size: 16px; font-weight: bold; border: 2px solid #14532d; padding: 6px 14px; display: inline-block; color: #14532d; }
        .comprobante .numero { font-size: 15px; margin-top: 6px; font-weight: bold; }
        .datos-proveedor { margin-bottom: 20px; border: 1px solid #ddd; padding: 12px 15px; border-radius: 4px; }
        .datos-proveedor .fila { display: flex; justify-content: space-between; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #14532d; color: #fff; text-align: left; padding: 8px; font-size: 12px; }
        td { padding: 8px; border-bottom: 1px solid #eee; font-size: 12px; }
        .text-end { text-align: right; }
        .firmas { display: flex; justify-content: space-between; margin-top: 60px; }
        .firma { width: 45%; text-align: center; }
        .firma .linea { border-top: 1px solid #333; margin-bottom: 6px; }
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
            <h1>{{ $compra->sucursal->empresa->razon_social ?? config('app.name') }}</h1>
            <div>{{ $compra->sucursal->empresa->direccion ?? '' }}</div>
            <div>{{ $compra->sucursal->nombre ?? '' }}</div>
        </div>
        <div class="comprobante">
            <div class="tipo">Remito de Recepción</div>
            <div class="numero">Compra N° {{ $compra->id_compra }}</div>
            <div>Comprobante: {{ $compra->tipo_comprobante }} {{ $compra->numero_comprobante }}</div>
        </div>
    </div>

    <div class="datos-proveedor">
        <div class="fila"><strong>Proveedor:</strong><span>{{ $compra->proveedor->razon_social_nombre ?? '—' }}</span></div>
        <div class="fila"><strong>Fecha de emisión:</strong><span>{{ $compra->fecha_emision->format('d/m/Y') }}</span></div>
        <div class="fila"><strong>Fecha de recepción:</strong><span>{{ $compra->fecha_recepcion->format('d/m/Y H:i') }}</span></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Producto</th>
                <th class="text-end">Cantidad Recibida</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compra->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->sku ?? '—' }}</td>
                <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad, 3, '.', ''), '0'), '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="firmas">
        <div class="firma">
            <div class="linea"></div>
            Firma y aclaración — Transportista
        </div>
        <div class="firma">
            <div class="linea"></div>
            Firma y aclaración — Recibido conforme
        </div>
    </div>

    <div class="pie">Documento generado por el sistema — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>