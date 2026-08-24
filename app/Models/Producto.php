<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'id_producto';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'sku',
        'codigo_barras',
        'nombre',
        'descripcion',
        'id_categoria',
        'id_marca',
        'id_impuesto',
        'unidad_medida',
        'precio_costo',
        'aplica_inventario',
        'stock_minimo',
        'stock_maximo',
        'estado',
    ];

    protected $casts = [
        'aplica_inventario' => 'boolean',
        'estado' => 'boolean',
        'precio_costo' => 'decimal:2',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca', 'id_marca');
    }

    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class, 'id_impuesto', 'id_impuesto');
    }
        public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto', 'id_producto');
    }

    public function detalleCompras()
    {
        return $this->hasMany(DetalleCompra::class, 'id_producto', 'id_producto');
    }
}