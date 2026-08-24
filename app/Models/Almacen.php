<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes';
    protected $primaryKey = 'id_almacen';
    public $timestamps = false;

    protected $fillable = [
        'id_sucursal',
        'nombre',
        'ubicacion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id_sucursal');
    }
}