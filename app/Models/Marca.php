<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use BelongsToEmpresa;
    
    protected $table = 'marcas';
    protected $primaryKey = 'id_marca';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];
}