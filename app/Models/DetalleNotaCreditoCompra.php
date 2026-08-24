<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleNotaCreditoCompra extends Model
{
    protected $table = 'detalle_notas_credito_compra';
    protected $primaryKey = 'id_detalle_ncc';
    public $timestamps = false;

    protected $fillable = [
        'id_nota_credito_compra',
        'id_producto',
        'cantidad',
        'costo_unitario',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function notaCredito()
    {
        return $this->belongsTo(NotaCreditoCompra::class, 'id_nota_credito_compra', 'id_nota_credito_compra');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}