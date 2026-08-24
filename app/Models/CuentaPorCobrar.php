<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuentas_por_cobrar';
    protected $primaryKey = 'id_cxc';
    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'id_cliente',
        'fecha_vencimiento',
        'monto_total',
        'monto_cobrado',
        'estado',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'monto_total' => 'decimal:2',
        'monto_cobrado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function cliente()
    {
        return $this->belongsTo(Persona::class, 'id_cliente', 'id_persona');
    }
}