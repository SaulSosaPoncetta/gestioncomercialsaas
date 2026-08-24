<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Remito de Transferencia {{ $transferencia->numero_remito }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #222; margin: 0; padding: 30px; }
        .encabezado { display: flex; justify-content: space-between; border-bottom: 3px solid #14532d; padding-bottom: 15px; margin-bottom: 20px; }
        .empresa h1 { margin: 0 0 4px; font-size: 20px; color: #14532d; }
        .empresa div { font-size: 12px; color: #555; }
        .comprobante { text-align: right; }
        .comprobante .tipo { font-size: 16px; font-weight: bold; border: 2px solid #14532d; padding: 6px 14px; display: inline-block; color: #14532d; }
        .comprobante .numero { font-size: 15px; margin-top: 6px; font-weight: bold; }
        .ruta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px; }
        .ruta .punto { text-align: center; flex: 1; }
        .ruta .punto .label { font-size: 11px; color: #888; text-transform: uppercase; }
        .ruta .punto .nombre { font-size: 15px; font-weight: bold; margin-top: 4px; }
        .ruta .flecha { font-size: 24px; color: #14532d; padding: 0 20px; }
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
            <h1>{{ $transferencia->almacenOrigen->sucursal->empresa->razon_social ?? config('app.name') }}</h1>
            <div>Documento de traslado interno — no válido como comprobante fiscal</div>
        </div>
        <div class="comprobante">
            <div class="tipo">Remito de Transferencia</div>
            <div class="numero">N° {{ $transferencia->numero_remito }}</div>
            <div>Fecha: {{ $transferencia->fecha_envio->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="ruta">
        <div class="punto">
            <div class="label">Origen</div>
            <div class="nombre">{{ $transferencia->almacenOrigen->nombre ?? '—' }}</div>
        </div>
        <div class="flecha">→</div>
        <div class="punto">
            <div class="label">Destino</div>
            <div class="nombre">{{ $transferencia->almacenDestino->nombre ?? '—' }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Producto</th>
                <th class="text-end">Cantidad Enviada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transferencia->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->sku ?? '—' }}</td>
                <td>{{ $detalle->producto->nombre ?? '—' }}</td>
                <td class="text-end">{{ rtrim(rtrim(number_format($detalle->cantidad_enviada, 3, '.', ''), '0'), '.') }}</td>
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
            Firma y aclaración — Recibe en destino
        </div>
    </div>

    <div class="pie">Documento generado por el sistema — {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>