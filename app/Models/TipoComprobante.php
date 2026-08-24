<?php

namespace App\Models;

use App\Traits\BelongsToEmpresa;
use Illuminate\Database\Eloquent\Model;

class TipoComprobante extends Model
{   
    use BelongsToEmpresa;
    
    protected $table = 'tipos_comprobante';
    protected $primaryKey = 'id_tipo_comprobante';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'descripcion',
        'serie_prefijo',
        'correlativo_actual',
    ];
}