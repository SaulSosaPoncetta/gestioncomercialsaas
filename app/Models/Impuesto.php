<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    use BelongsToEmpresa;
    
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