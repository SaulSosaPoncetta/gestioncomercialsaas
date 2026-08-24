<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';
    protected $primaryKey = 'id_stock';
    public $timestamps = false;

    protected $fillable = [
        'id_producto',
        'id_almacen',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'decimal:3',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'id_almacen', 'id_almacen');
    }
}