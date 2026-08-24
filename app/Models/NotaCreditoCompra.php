<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoCompra extends Model
{
    protected $table = 'notas_credito_compra';
    protected $primaryKey = 'id_nota_credito_compra';
    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'numero_nota',
        'fecha',
        'motivo',
        'subtotal',
        'total',
        'estado',
        'id_usuario',
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra', 'id_compra');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleNotaCreditoCompra::class, 'id_nota_credito_compra', 'id_nota_credito_compra');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id');
    }
}