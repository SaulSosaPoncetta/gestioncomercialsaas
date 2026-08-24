<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleNotaCreditoVenta extends Model
{
    protected $table = 'detalle_notas_credito_venta';
    protected $primaryKey = 'id_detalle_ncv';
    public $timestamps = false;

    protected $fillable = [
        'id_nota_credito_venta',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'monto_impuesto',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'precio_unitario' => 'decimal:2',
        'monto_impuesto' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function notaCredito()
    {
        return $this->belongsTo(NotaCreditoVenta::class, 'id_nota_credito_venta', 'id_nota_credito_venta');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}
