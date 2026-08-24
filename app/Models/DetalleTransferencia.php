<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleTransferencia extends Model
{
    protected $table = 'detalle_transferencias';
    protected $primaryKey = 'id_detalle_transferencia';
    public $timestamps = false;

    protected $fillable = [
        'id_transferencia',
        'id_producto',
        'cantidad_enviada',
        'cantidad_recibida',
    ];

    protected $casts = [
        'cantidad_enviada' => 'decimal:3',
        'cantidad_recibida' => 'decimal:3',
    ];

    public function transferencia()
    {
        return $this->belongsTo(Transferencia::class, 'id_transferencia', 'id_transferencia');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function diferencia()
    {
        if ($this->cantidad_recibida === null) {
            return null;
        }
        return $this->cantidad_recibida - $this->cantidad_enviada;
    }
}