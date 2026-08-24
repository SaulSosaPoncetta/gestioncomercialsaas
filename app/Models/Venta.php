<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    const CREATED_AT = 'fecha_venta';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_sucursal',
        'id_cliente',
        'id_vendedor',
        'id_sesion_caja',
        'tipo_venta',
        'subtotal',
        'descuento_total',
        'impuesto_total',
        'total_venta',
        'estado',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'descuento_total' => 'decimal:2',
        'impuesto_total' => 'decimal:2',
        'total_venta' => 'decimal:2',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function cliente()
    {
        return $this->belongsTo(Persona::class, 'id_cliente', 'id_persona');
    }

    public function vendedor()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_vendedor', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta', 'id_venta');
    }

    public function cuentaPorCobrar()
    {
        return $this->hasOne(CuentaPorCobrar::class, 'id_venta', 'id_venta');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class, 'id_venta', 'id_venta');
    }
}
