<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PrecioProducto extends Model
{
    protected $table = 'precios_productos';
    protected $primaryKey = 'id_precio_producto';
    public $timestamps = false;

    protected $fillable = [
        'id_lista_precio',
        'id_producto',
        'precio_venta',
    ];

    protected $casts = [
        'precio_venta' => 'decimal:2',
    ];


    /**
     * Multi-empresa: esta tabla no tiene id_empresa; se filtra por la lista de precios,
     * que sí está filtrada por la empresa del usuario logueado.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('empresa', function (Builder $builder) {
            if (auth()->check() && ! auth()->user()->hasRole('superadmin')) {
                $builder->whereHas('listaPrecio');
            }
        });
    }
    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class, 'id_lista_precio', 'id_lista_precio');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}