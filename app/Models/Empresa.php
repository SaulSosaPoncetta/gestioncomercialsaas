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
    'estado',
    'estado_pago',
    'fecha_verificacion_pago',
];
protected $casts = [
    'estado' => 'boolean',
    'fecha_verificacion_pago' => 'datetime',
];

public function alDiaConElPago(): bool
{
    return in_array($this->estado_pago, ['activa', 'sin_verificar'], true);
}

    public function sucursales()
    {
        return $this->hasMany(Sucursal::class, 'id_empresa', 'id_empresa');
    }
}