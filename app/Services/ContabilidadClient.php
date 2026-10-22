<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\NotaCreditoCompra;
use App\Models\NotaCreditoVenta;
use App\Models\Persona;
use App\Models\SincronizacionContable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envía facturas, notas de crédito y (opcionalmente) compras al sistema contable de MiGestión.
 * El sistema contable genera el comprobante y su asiento. Los envíos son idempotentes:
 * reenviar el mismo documento lo actualiza, no lo duplica.
 */
class ContabilidadClient
{
    /** Ventas con esta marca no se informan (documento no fiscal) */
    private const SIN_FACTURA = '__omitir__';

    /* ================================================================
     | Envío
     * ================================================================ */

    /** Sincroniza un documento. Devuelve el registro con el resultado (o null si la empresa no integra). */
    public function sincronizar(string $tipo, int $id): ?SincronizacionContable
    {
        [$modelo, $empresa] = $this->cargar($tipo, $id);

        if (! $modelo || ! $empresa || ! $empresa->integraConContabilidad()) {
            return null;
        }
        if (in_array($tipo, ['compra', 'nota_credito_compra'], true) && ! $empresa->contabilidad_compras) {
            return null;
        }

        $sinc = SincronizacionContable::firstOrNew([
            'id_empresa'  => $empresa->id_empresa,
            'tipo_origen' => $tipo,
            'id_externo'  => (string) $id,
        ]);
        $sinc->intentos = ($sinc->intentos ?? 0) + 1;

        try {
            $payload = $this->payload($tipo, $modelo);

            if ($payload === self::SIN_FACTURA) {
                return $this->guardar($sinc, 'omitido', 'Documento no fiscal (la venta no tiene factura): no se informa a la contabilidad.');
            }

            if ($empresa->contabilidad_desde && $payload['fecha'] < $empresa->contabilidad_desde->toDateString()) {
                return $this->guardar($sinc, 'omitido', 'Anterior a la fecha de inicio de la integración.');
            }

            $respuesta = Http::withToken($empresa->contabilidad_token)
                ->acceptJson()
                ->timeout((int) config('contabilidad.timeout', 10))
                ->post($empresa->urlContabilidad() . '/api/integracion/v1/comprobantes', $payload);

            $json = $respuesta->json() ?? [];

            if ($respuesta->successful() && ($json['ok'] ?? false)) {
                $texto = ucfirst($json['accion'] ?? 'enviado') . ': ' . ($json['comprobante'] ?? '')
                    . (isset($json['asiento_numero']) ? ' · asiento N° ' . $json['asiento_numero'] : '');

                return $this->guardar($sinc, 'ok', $texto);
            }

            return $this->guardar($sinc, 'error', $json['error'] ?? ('HTTP ' . $respuesta->status()));
        } catch (\Throwable $e) {
            Log::warning('Integración contable: error al enviar', ['tipo' => $tipo, 'id' => $id, 'error' => $e->getMessage()]);

            return $this->guardar($sinc, 'error', 'No se pudo enviar: ' . $e->getMessage());
        }
    }

    /** Avisa la anulación de un documento ya enviado. */
    public function anular(string $tipo, int $id, int $idEmpresa, string $motivo): bool
    {
        $empresa = Empresa::find($idEmpresa);
        if (! $empresa || ! $empresa->integraConContabilidad()) {
            return false;
        }

        try {
            $r = Http::withToken($empresa->contabilidad_token)->acceptJson()
                ->timeout((int) config('contabilidad.timeout', 10))
                ->post($empresa->urlContabilidad() . '/api/integracion/v1/comprobantes/anular', [
                    'tipo_origen' => $tipo, 'id_externo' => (string) $id, 'motivo' => $motivo,
                ]);

            return $r->successful();
        } catch (\Throwable $e) {
            Log::warning('Integración contable: error al anular', ['tipo' => $tipo, 'id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /** Prueba de conexión. @return array ['ok' => bool, 'mensaje' => string] */
    public function probar(Empresa $empresa): array
    {
        if (! $empresa->urlContabilidad() || ! $empresa->contabilidad_token) {
            return ['ok' => false, 'mensaje' => 'Falta la URL o el token.'];
        }

        try {
            $r = Http::withToken($empresa->contabilidad_token)->acceptJson()->timeout(8)
                ->post($empresa->urlContabilidad() . '/api/integracion/v1/ping');
            $json = $r->json() ?? [];

            return $r->successful() && ($json['ok'] ?? false)
                ? ['ok' => true, 'mensaje' => 'Conectado con la contabilidad de ' . ($json['empresa'] ?? '?') . ' (CUIT ' . ($json['cuit'] ?? '?') . ').']
                : ['ok' => false, 'mensaje' => $json['error'] ?? ('Respuesta HTTP ' . $r->status())];
        } catch (\Throwable $e) {
            return ['ok' => false, 'mensaje' => 'No se pudo conectar: ' . $e->getMessage()];
        }
    }

    /* ================================================================
     | Armado de los datos
     * ================================================================ */

    private function cargar(string $tipo, int $id): array
    {
        $modelo = match ($tipo) {
            'factura'             => Factura::with(['venta' => fn ($q) => $q->withoutGlobalScopes(), 'tipoComprobante' => fn ($q) => $q->withoutGlobalScopes()])->find($id),
            'nota_credito_venta'  => NotaCreditoVenta::withoutGlobalScopes()->find($id),
            'compra'              => Compra::withoutGlobalScopes()->find($id),
            'nota_credito_compra' => NotaCreditoCompra::withoutGlobalScopes()->find($id),
            default               => null,
        };

        $idEmpresa = match ($tipo) {
            'factura' => $modelo?->venta?->id_empresa,
            default   => $modelo?->id_empresa,
        };

        return [$modelo, $idEmpresa ? Empresa::find($idEmpresa) : null];
    }

    private function payload(string $tipo, $m): array|string
    {
        return match ($tipo) {
            'factura'             => $this->payloadFactura($m),
            'nota_credito_venta'  => $this->payloadNotaCreditoVenta($m),
            'compra'              => $this->payloadCompra($m),
            'nota_credito_compra' => $this->payloadNotaCreditoCompra($m),
        };
    }

    private function payloadFactura(Factura $f): array
    {
        $venta = $f->venta;
        $cliente = Persona::withoutGlobalScopes()->find($venta->id_cliente);
        $tipo = $this->tipoDesdeCodigo($f->tipoComprobante?->codigo, $f->tipoComprobante?->descripcion, 'factura');
        [$pv, $numero] = $this->separarNumero($f->numero_factura, $f->tipoComprobante?->serie_prefijo);

        $detalles = $venta->detalles()->with(['producto' => fn ($q) => $q->withoutGlobalScopes()->with(['impuesto' => fn ($i) => $i->withoutGlobalScopes()])])->get();
        $alicuotas = $this->alicuotasVenta($detalles, fn ($d) => (float) $d->cantidad * (float) $d->precio_unitario - (float) $d->descuento);
        $alicuotas = $this->ajustar($alicuotas, (float) $f->subtotal, (float) $f->monto_iva);

        return [
            'tipo_origen'      => 'factura',
            'id_externo'       => (string) $f->id_factura,
            'circuito'         => 'venta',
            'tipo_comprobante' => $tipo,
            'punto_venta'      => $pv,
            'numero'           => $numero,
            'fecha'            => $f->fecha_emision->toDateString(),
            'cae'              => $f->cae_fiscal,
            'vto_cae'          => $f->vencimiento_cae?->toDateString(),
            'tercero'          => $this->tercero($cliente),
            'alicuotas'        => $alicuotas,
            'total'            => round((float) $f->total_facturado, 2),
            'observaciones'    => 'GestiónComercial · venta #' . $venta->id_venta,
        ];
    }

    private function payloadNotaCreditoVenta(NotaCreditoVenta $nc): array|string
    {
        $venta = $nc->venta()->withoutGlobalScopes()->first();
        $factura = $venta ? Factura::with(['tipoComprobante' => fn ($q) => $q->withoutGlobalScopes()])->where('id_venta', $venta->id_venta)->first() : null;

        if (! $factura) {
            return self::SIN_FACTURA;
        }

        $cliente = Persona::withoutGlobalScopes()->find($venta->id_cliente);
        $letra = $this->letra($this->tipoDesdeCodigo($factura->tipoComprobante?->codigo, $factura->tipoComprobante?->descripcion, 'factura'));
        [$pv] = $this->separarNumero($factura->numero_factura, $factura->tipoComprobante?->serie_prefijo);

        $detalles = $nc->detalles()->with(['producto' => fn ($q) => $q->withoutGlobalScopes()->with(['impuesto' => fn ($i) => $i->withoutGlobalScopes()])])->get();
        $alicuotas = $this->alicuotasVenta($detalles, fn ($d) => (float) $d->cantidad * (float) $d->precio_unitario);
        $alicuotas = $this->ajustar($alicuotas, (float) $nc->subtotal, (float) $nc->monto_impuesto);

        return [
            'tipo_origen'      => 'nota_credito_venta',
            'id_externo'       => (string) $nc->id_nota_credito_venta,
            'circuito'         => 'venta',
            'tipo_comprobante' => ['A' => 3, 'B' => 8, 'C' => 13, 'M' => 53][$letra] ?? 8,
            'punto_venta'      => $pv,
            'numero'           => $this->soloNumero($nc->numero_nota) ?: $nc->id_nota_credito_venta,
            'fecha'            => $nc->fecha->toDateString(),
            'tercero'          => $this->tercero($cliente),
            'alicuotas'        => $alicuotas,
            'total'            => round((float) $nc->total, 2),
            'observaciones'    => 'GestiónComercial · NC de la factura ' . $factura->numero_factura,
        ];
    }

    /**
     * Compras: GestiónComercial no registra el IVA de las compras.
     * En comprobantes A/M se estima con la alícuota del impuesto de cada producto (costo = neto).
     */
    private function payloadCompra(Compra $c): array
    {
        $proveedor = Persona::withoutGlobalScopes()->find($c->id_proveedor);
        [$clase, $letra] = $this->claseYLetra($c->tipo_comprobante);
        [$pv, $numero] = $this->separarNumero($c->numero_comprobante, null);

        $tipo = $this->codigoArca($clase, $letra);
        $detalles = $c->detalles()->with(['producto' => fn ($q) => $q->withoutGlobalScopes()->with(['impuesto' => fn ($i) => $i->withoutGlobalScopes()])])->get();

        return $this->payloadCompraComun('compra', $c->id_compra, $tipo, $pv, $numero, $c->fecha_emision->toDateString(),
            $proveedor, $detalles, (float) $c->total_compra, 'GestiónComercial · compra #' . $c->id_compra);
    }

    private function payloadNotaCreditoCompra(NotaCreditoCompra $nc): array
    {
        $compra = $nc->compra()->withoutGlobalScopes()->first();
        $proveedor = Persona::withoutGlobalScopes()->find($compra->id_proveedor);
        [, $letra] = $this->claseYLetra($compra->tipo_comprobante);
        [$pv] = $this->separarNumero($compra->numero_comprobante, null);

        $detalles = $nc->detalles()->with(['producto' => fn ($q) => $q->withoutGlobalScopes()->with(['impuesto' => fn ($i) => $i->withoutGlobalScopes()])])->get();

        return $this->payloadCompraComun('nota_credito_compra', $nc->id_nota_credito_compra, $this->codigoArca('credito', $letra), $pv,
            $this->soloNumero($nc->numero_nota) ?: $nc->id_nota_credito_compra, $nc->fecha->toDateString(),
            $proveedor, $detalles, (float) $nc->total, 'GestiónComercial · NC de la compra ' . $compra->numero_comprobante);
    }

    private function payloadCompraComun(string $origen, int $id, int $tipo, int $pv, int $numero, string $fecha, ?Persona $prov, $detalles, float $totalSinIva, string $obs): array
    {
        $letra = $this->letra($tipo);
        $alicuotas = [];
        $total = $totalSinIva;

        if (in_array($letra, ['A', 'M'], true)) {
            $grupos = [];
            foreach ($detalles as $d) {
                $pct = (string) round((float) ($d->producto?->impuesto?->porcentaje ?? 21), 2);
                $grupos[$pct] = ($grupos[$pct] ?? 0) + (float) $d->cantidad * (float) $d->costo_unitario;
            }
            foreach ($grupos as $pct => $neto) {
                $iva = round($neto * (float) $pct / 100, 2);
                $alicuotas[] = ['porcentaje' => (float) $pct, 'neto' => round($neto, 2), 'iva' => $iva];
            }
            $total = round(array_sum(array_map(fn ($a) => $a['neto'] + $a['iva'], $alicuotas)), 2);
            $obs .= ' · IVA estimado según la alícuota de los productos: verificar contra la factura';
        }

        return [
            'tipo_origen'      => $origen,
            'id_externo'       => (string) $id,
            'circuito'         => 'compra',
            'tipo_comprobante' => $tipo,
            'punto_venta'      => $pv,
            'numero'           => $numero,
            'fecha'            => $fecha,
            'tercero'          => $this->tercero($prov),
            'alicuotas'        => $alicuotas,
            'no_gravado'       => $alicuotas ? 0 : round($total, 2),
            'total'            => round($total, 2),
            'observaciones'    => mb_substr($obs, 0, 255),
        ];
    }

    /* ---------------------------------------------------------------- */

    /** Agrupa renglones por porcentaje del impuesto del producto: neto (según $base) e IVA (monto_impuesto). */
    private function alicuotasVenta($detalles, callable $base): array
    {
        $g = [];
        foreach ($detalles as $d) {
            $pct = (string) round((float) ($d->producto?->impuesto?->porcentaje ?? 0), 2);
            $g[$pct]['neto'] = ($g[$pct]['neto'] ?? 0) + $base($d);
            $g[$pct]['iva'] = ($g[$pct]['iva'] ?? 0) + (float) $d->monto_impuesto;
        }

        return array_values(array_map(
            fn ($pct, $v) => ['porcentaje' => (float) $pct, 'neto' => round($v['neto'], 2), 'iva' => round($v['iva'], 2)],
            array_keys($g), $g
        ));
    }

    /** Ajusta diferencias de redondeo para que los totales coincidan con el documento. */
    private function ajustar(array $alicuotas, float $neto, float $iva): array
    {
        if (! $alicuotas) {
            return $alicuotas;
        }
        $difNeto = round($neto - array_sum(array_column($alicuotas, 'neto')), 2);
        $difIva = round($iva - array_sum(array_column($alicuotas, 'iva')), 2);
        if (abs($difNeto) <= 1) {
            $alicuotas[0]['neto'] = round($alicuotas[0]['neto'] + $difNeto, 2);
        }
        if (abs($difIva) <= 1) {
            $alicuotas[0]['iva'] = round($alicuotas[0]['iva'] + $difIva, 2);
        }

        return $alicuotas;
    }

    private function tercero(?Persona $p): array
    {
        if (! $p) {
            return ['razon_social' => 'Consumidor final', 'tipo_documento' => 99, 'nro_documento' => null, 'condicion_iva' => 5];
        }

        $tipoDoc = match (strtoupper((string) $p->tipo_documento)) {
            'CUIT' => 80, 'CUIL' => 86, 'DNI' => 96, 'PASAPORTE' => 94, default => 99,
        };
        $nro = preg_replace('/\D/', '', (string) $p->numero_documento);
        if ($nro === '' || (int) $nro === 0) {
            $tipoDoc = 99;
            $nro = null;
        }

        $cond = mb_strtolower((string) $p->condicion_iva);
        $condicion = match (true) {
            str_contains($cond, 'monotrib')                                     => 6,
            str_contains($cond, 'exento')                                       => 4,
            str_contains($cond, 'no categ')                                     => 7,
            str_contains($cond, 'exterior')                                     => 9,
            str_contains($cond, 'inscripto') && ! str_contains($cond, 'no ins') => 1,
            default                                                             => 5,
        };

        return [
            'razon_social'   => $p->razon_social_nombre,
            'tipo_documento' => $tipoDoc,
            'nro_documento'  => $nro,
            'condicion_iva'  => $condicion,
            'domicilio'      => $p->direccion,
            'email'          => $p->email,
            'telefono'       => $p->telefono,
        ];
    }

    /** FACT_A → 1 · FACT_B → 6 · FACT_C → 11 · FACT_M → 51 · TICKET → 83 · códigos ARCA numéricos se respetan */
    private function tipoDesdeCodigo(?string $codigo, ?string $descripcion, string $clase): int
    {
        $c = strtoupper(trim((string) $codigo));
        if (ctype_digit($c)) {
            return (int) $c;
        }
        if (str_contains($c, 'TICKET') || str_contains($c, 'TIQUE')) {
            return 83;
        }
        [, $letra] = $this->claseYLetra($c . ' ' . $descripcion);

        return $this->codigoArca($clase, $letra);
    }

    /** Interpreta textos como «FACTURA A», «FA», «NC B», «NOTA DE CREDITO C», «FACT_A» */
    private function claseYLetra(?string $texto): array
    {
        $t = strtoupper(str_replace(['_', '-', '.'], ' ', (string) $texto));
        $clase = (str_contains($t, 'CRED') || preg_match('/\bNC\b/', $t)) ? 'credito'
            : ((str_contains($t, 'DEB') || preg_match('/\bND\b/', $t)) ? 'debito' : 'factura');

        $letra = 'B';
        if (preg_match('/\b([ABCM])\b/', $t, $m)) {
            $letra = $m[1];
        } elseif (preg_match('/\b(?:F|FC|FACT|NC|ND)([ABCM])\b/', $t, $m)) {
            $letra = $m[1];
        }

        return [$clase, $letra];
    }

    private function codigoArca(string $clase, string $letra): int
    {
        $tabla = [
            'factura' => ['A' => 1, 'B' => 6, 'C' => 11, 'M' => 51],
            'debito'  => ['A' => 2, 'B' => 7, 'C' => 12, 'M' => 52],
            'credito' => ['A' => 3, 'B' => 8, 'C' => 13, 'M' => 53],
        ];

        return $tabla[$clase][$letra] ?? $tabla[$clase]['B'];
    }

    private function letra(int $tipo): string
    {
        return match (true) {
            in_array($tipo, [1, 2, 3, 81], true)    => 'A',
            in_array($tipo, [11, 12, 13], true)     => 'C',
            in_array($tipo, [51, 52, 53], true)     => 'M',
            default                                 => 'B',
        };
    }

    /** «F001-00004521» → [1, 4521] · «0001-00001234» → [1, 1234] · «1234» → [prefijo o 1, 1234] */
    private function separarNumero(?string $numero, ?string $prefijo): array
    {
        $partes = preg_split('/[-\s\/]+/', trim((string) $numero));
        if (count($partes) >= 2) {
            $pv = (int) preg_replace('/\D/', '', $partes[0]);
            $nro = (int) preg_replace('/\D/', '', end($partes));
        } else {
            $pv = (int) preg_replace('/\D/', '', (string) $prefijo);
            $nro = (int) preg_replace('/\D/', '', (string) $numero);
        }

        return [max(1, min($pv ?: 1, 99999)), max(1, $nro % 100000000)];
    }

    private function soloNumero(?string $texto): int
    {
        return (int) substr(preg_replace('/\D/', '', (string) $texto), -8);
    }

    private function guardar(SincronizacionContable $s, string $estado, string $mensaje): SincronizacionContable
    {
        $s->fill(['estado' => $estado, 'mensaje' => mb_substr($mensaje, 0, 1000), 'enviado_at' => now()])->save();

        return $s;
    }
}
