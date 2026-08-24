<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'id_movimiento';
    const CREATED_AT = 'fecha';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_producto',
        'id_almacen_origen',
        'id_almacen_destino',
        'tipo_movimiento',
        'cantidad',
        'costo_unitario',
        'referencia_documento',
        'motivo',
        'id_usuario',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
        'costo_unitario' => 'decimal:2',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen_origen', 'id_almacen');
    }

    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen_destino', 'id_almacen');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id');
    }
}