<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';
    protected $primaryKey = 'id_empresa';
    public $timestamps = false;

    protected $fillable = [
        'razon_social',
        'nombre_fantasia',
        'identificacion_fiscal',
        'direccion',
        'telefono',
        'email',
    ];

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }
}