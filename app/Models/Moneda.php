<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table = 'monedas';
    protected $primaryKey = 'id_moneda';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'simbolo',
        'tipo_cambio',
        'es_moneda_base',
    ];

    protected $casts = [
        'es_moneda_base' => 'boolean',
        'tipo_cambio' => 'decimal:4',
    ];
}