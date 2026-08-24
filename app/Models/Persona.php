<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'id_persona';
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $fillable = [
        'tipo_persona',
        'razon_social_nombre',
        'tipo_documento',
        'numero_documento',
        'condicion_iva',
        'direccion',
        'telefono',
        'email',
        'limite_credito',
        'dias_credito',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'limite_credito' => 'decimal:2',
    ];

    public function scopeClientes($query)
    {
        return $query->whereIn('tipo_persona', ['CLIENTE', 'AMBOS']);
    }

    public function scopeProveedores($query)
    {
        return $query->whereIn('tipo_persona', ['PROVEEDOR', 'AMBOS']);
    }
}