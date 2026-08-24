<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $primaryKey = 'id_compra';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor',
        'id_sucursal',
        'tipo_comprobante',
        'numero_comprobante',
        'fecha_emision',
        'fecha_recepcion',
        'condicion_pago',
        'subtotal',
        'total_impuestos',
        'total_compra',
        'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_recepcion' => 'datetime',
        'subtotal' => 'decimal:2',
        'total_impuestos' => 'decimal:2',
        'total_compra' => 'decimal:2',
    ];

    public function proveedor()
    {
        return $this->belongsTo(Persona::class, 'id_proveedor', 'id_persona');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra', 'id_compra');
    }

    public function cuentaPorPagar()
    {
        return $this->hasOne(CuentaPorPagar::class, 'id_compra', 'id_compra');
    }
}