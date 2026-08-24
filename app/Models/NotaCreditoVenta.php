<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoVenta extends Model
{
    protected $table = 'notas_credito_venta';
    protected $primaryKey = 'id_nota_credito_venta';
    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'numero_nota',
        'fecha',
        'motivo',
        'subtotal',
        'monto_impuesto',
        'total',
        'estado',
        'id_usuario',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'monto_impuesto' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleNotaCreditoVenta::class, 'id_nota_credito_venta', 'id_nota_credito_venta');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id');
    }
}