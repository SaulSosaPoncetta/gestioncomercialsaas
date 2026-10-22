<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Estado del envío de cada documento al sistema contable.
 * (Sin el trait de empresa: el filtro se hace explícito por id_empresa.)
 */
class SincronizacionContable extends Model
{
    protected $table = 'sincronizaciones_contables';

    public const TIPOS = [
        'factura'             => 'Factura de venta',
        'nota_credito_venta'  => 'Nota de crédito de venta',
        'compra'              => 'Compra',
        'nota_credito_compra' => 'Nota de crédito de compra',
    ];

    protected $fillable = [
        'id_empresa', 'tipo_origen', 'id_externo', 'estado', 'mensaje', 'intentos', 'enviado_at',
    ];

    protected $casts = [
        'enviado_at' => 'datetime',
    ];
}
