<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected $primaryKey = 'id_sucursal';
    public $timestamps = false;

    protected $fillable = [
        'id_empresa',
        'nombre',
        'codigo',
        'direccion',
        'telefono',
        'es_casa_matriz',
        'estado',
    ];

    protected $casts = [
        'es_casa_matriz' => 'boolean',
        'estado' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'id_empresa', 'id_empresa');
    }
}