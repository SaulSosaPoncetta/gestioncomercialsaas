<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    protected $table = 'impuestos';
    protected $primaryKey = 'id_impuesto';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'porcentaje',
        'es_predeterminado',
        'estado',
    ];

    protected $casts = [
        'es_predeterminado' => 'boolean',
        'estado' => 'boolean',
        'porcentaje' => 'decimal:2',
    ];
}