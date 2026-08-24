<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class ListaPrecio extends Model
{
    use BelongsToEmpresa;
    
    protected $table = 'listas_precios';
    protected $primaryKey = 'id_lista_precio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'porcentaje_ganancia_base',
        'es_predeterminada',
    ];

    protected $casts = [
        'es_predeterminada' => 'boolean',
        'porcentaje_ganancia_base' => 'decimal:2',
    ];

    public function precios()
    {
        return $this->hasMany(PrecioProducto::class, 'id_lista_precio', 'id_lista_precio');
    }
}