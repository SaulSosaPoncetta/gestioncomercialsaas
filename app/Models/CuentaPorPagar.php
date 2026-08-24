<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorPagar extends Model
{
    protected $table = 'cuentas_por_pagar';
    protected $primaryKey = 'id_cxp';
    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'id_proveedor',
        'fecha_vencimiento',
        'monto_total',
        'monto_pagado',
        'estado',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'monto_total' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id_compra');
    }

    public function proveedor()
    {
        return $this->belongsTo(Persona::class, 'id_proveedor', 'id_persona');
    }
}